<?php

class dmErrorNotifier
{

  public static function notify(sfEvent $event)
  {
    try
    {
      if (sfContext::hasInstance())
      {
        if (sfConfig::get('dm_error_mail_superadmin') || sfConfig::get('dm_error_store_in_db'))
        {
          $error = new dmErrorDescription($event->getSubject(), sfContext::getInstance());

          if(sfConfig::get('dm_error_mail_superadmin'))
          {
            self::mailSuperadmin($error);
          }

          if(sfConfig::get('dm_error_store_in_db'))
          {
            self::storeInDb($error);
          }
        }
      }
    }
    catch(Exception $e)
    {
      die("Exception $e thrown while notifying exception ".$event->getSubject());
    }
  }

  protected static function mailSuperadmin(dmErrorDescription $error)
  {
    if (!$superAdmin = dmDb::query('sfGuardUser u')->where('u.is_super_admin = ?', true)->fetchRecord())
    {
      return;
    }
    if (!$superAdminEmail = $superAdmin->email)
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

  protected static function storeInDb(dmErrorDescription $error)
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