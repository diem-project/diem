<?php

class dmErrorWatcher extends dmConfigurable
{
  protected
  $dispatcher,
  $context,
  $options;
  
  public function __construct(sfEventDispatcher $dispatcher, dmContext $context, array $options = array())
  {
    $this->dispatcher = $dispatcher;
    $this->context    = $context;
    
    $this->configure($options);
  }
  
  public function getDefaultOptions()
  {
    return array(
      'error_description_class' => 'dmErrorDescription',
      'mail_superadmin'         => false,
      'store_in_db'             => false
    );
  }
  
  public function connect()
  {
    $this->dispatcher->connect('application.throw_exception', array($this, 'listenToThrowException'));
  }
  
  public function listenToThrowException(sfEvent $event)
  {
    $this->handleException($event->getSubject());
  }

  public function handleException(Exception $exception)
  {
    try
    {
      if ($this->getOption('mail_superadmin') || $this->getOption('store_in_db'))
      {
        $error = new $this->options['error_description_class']($exception, $this->context);

        if($this->getOption('mail_superadmin'))
        {
          $this->mailSuperadmin($error);
        }

        if($this->getOption('store_in_db'))
        {
          $this->storeInDb($error);
        }
      }
    }
    catch(Exception $e)
    {
      die(sprintf('Exception %s thrown while notifying exception %s', $e, $exception));
    }
  }

  protected function mailSuperadmin(dmErrorDescription $error)
  {
    if (!$superAdmin = $this->getSuperadmin())
    {
      return;
    }
    
    $subject = "Exception - {$error->env} - {$error->name}";
    $body = "Exception notification for the environment {$error->env} - {$error->date}\n\n";
    $body .= $error->exception . "\n\n\n\n\n";
    $body .= "Additional data: \n\n";
    foreach(array('class', 'name', 'module', 'action', 'uri') as $attribute)
    {
      $body .= $attribute . " => " . $error->$attribute . "\n\n";
    }

    mail($superAdmin->email, $subject, $body);
  }
  
  protected function getSuperadmin()
  {
    return dmDb::query('DmUser u')->where('u.is_super_admin = ?', true)->fetchRecord();
  }

  protected function storeInDb(dmErrorDescription $error)
  {
    dmDb::create('DmError', array(
      'description' => $error->name."\n".$error->exception->getTraceAsString(),
      'php_class' => $error->class,
      'name' => dmString::truncate($error->name, 255, ''),
      'module' => $error->module,
      'action' => is_object($error->action) ? $error->action->getActionName() : $error->action,
      'uri' => dmString::truncate($error->uri, 255),
      'env' => $error->env,
    ))->save();
  }

}

class dmErrorDescription
{
  public
  $exception,
  $class,
  $name,
  $module,
  $action,
  $uri,
  $env,
  $date;

  public function __construct(Exception $e, dmContext $context)
  {
    $this->exception = $e;
    $this->class = get_class($e);
    $this->name = $e->getMessage() ? $e->getMessage() : 'n/a';
    $this->module = $context->getModuleName();
    $this->action = $context->getActionName();
    $this->uri = $context->getRequest()->getUri();

    $env = 'n/a';
    if ($conf = $context->getConfiguration())
    {
      $env = $conf->getEnvironment();
    }
    $this->env = $env;

    $this->date = date('H:i:s j F Y');
  }
}