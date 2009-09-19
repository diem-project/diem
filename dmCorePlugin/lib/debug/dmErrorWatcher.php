<?php

class dmErrorWatcher
{
  protected
  $dispatcher,
  $context,
  $options;
  
  public function __construct(sfEventDispatcher $dispatcher, sfContext $context, array $options = array())
  {
    $this->dispatcher = $dispatcher;
    $this->context    = $context;
    
    $this->initialize($options);
  }
  
  public function initialize(array $options = array())
  {
    $this->options = array_merge(array(
      'error_description_class' => 'dmErrorDescription'
    ), $options);
  }
  
  public function connect()
  {
    $this->dispatcher->connect('application.throw_exception', array($this, 'listenToThrowException'));
  }
  
  public function listenToThrowException(sfEvent $event)
  {
    try
    {
      if (sfConfig::get('dm_error_mail_superadmin') || sfConfig::get('dm_error_store_in_db'))
      {
        $error = new $this->options['error_description_class']($event->getSubject(), $this->context);

        if(sfConfig::get('dm_error_mail_superadmin'))
        {
          $this->mailSuperadmin($error);
        }

        if(sfConfig::get('dm_error_store_in_db'))
        {
          $this->storeInDb($error);
        }
      }
    }
    catch(Exception $e)
    {
      die(sprintf('Exception %s thrown while notifying exception %s', $e, $event->getSubject()));
    }
  }

  protected function mailSuperadmin(dmErrorDescription $error)
  {
    if (!$superAdmin = dmDb::query('sfGuardUser u')->where('u.is_super_admin = ?', true)->fetchRecord())
    {
      return;
    }
    
    if (!$superAdminEmail = $superAdmin->get('email'))
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

    //mail($superAdminEmail, $subject, $body);
  }

  protected function storeInDb(dmErrorDescription $error)
  {
    dmDb::create('DmError', array(
      'description' => $error->name."\n".$error->exception->getTraceAsString(),
      'klass' => $error->class,
      'name' => dmString::truncate($error->name, 255, ''),
      'module' => $error->module,
      'action' => $error->action,
      'uri' => $error->uri,
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

  public function __construct(Exception $e, sfContext $context)
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