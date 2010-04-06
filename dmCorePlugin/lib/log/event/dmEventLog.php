<?php

class dmEventLog extends dmFileLog
{
  protected
  $fields = array(
    'time',
    'ip',
    'session_id',
    'user_id',
    'action',
    'type',
    'subject',
    'record'
  );

  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'name'                => 'Events',
      'file'                => 'data/dm/log/event.log',
      'entry_service_name'  => 'event_log_entry',
      'ignore_models'       => array(),
      'ignore_internal_actions' => true
    ));
  }
  
  public function getConnections()
  {
    return array(
      'dm.record.modification' => 'listenToRecordModificationEvent',
      'dm.record.creation' => 'listenToRecordCreationEvent',
      'application.throw_exception' => 'listenToThrowExceptionEvent',
      'user.sign_in' => 'listenToUserSignInEvent',
      'user.sign_out' => 'listenToUserSignOutEvent',
      'dm.cache.clear' => 'listenToCacheClearEvent',
      'dm.sitemap.generated' => 'listenToSitemapUpdatedEvent',
      'dm.search.populated' => 'listenToSearchUpdatedEvent',
      'dm.config.updated' => 'listenToConfigUpdatedEvent',
      'dm.mail.post_send' => 'listenToMailPostSendEvent'
    );
  }
  
  public function connect()
  {
    $dispatcher = $this->serviceContainer->getService('dispatcher');

    foreach($this->getConnections() as $event => $method)
    {
      $dispatcher->connect($event, array($this, $method));
    }
  }

  public function listenToMailPostSendEvent(sfEvent $event)
  {
    $sentMail = dmDb::table('DmSentMail')->createFromSwiftMessage($event['message'])->merge(array(
      'dm_mail_template_id' => isset($event['template']) ? $event['template']->get('id') : null,
      'strategy'            => $event['mailer']->getDeliveryStrategy(),
      'transport'           => get_class($event['mailer']->getTransport()),
    ))->saveGet();

    $this->log(array(
      'server'  => $_SERVER,
      'action'  => 'send',
      'type'    => 'mail',
      'subject' => isset($event['template']) ? $event['template']->get('name') : $event['message']->getSubject(),
      'record'  => $sentMail
    ));
  }
  
  public function listenToConfigUpdatedEvent(sfEvent $event)
  {
    $setting = $event['setting'];
    
    if ('internal' == dmString::strtolower($setting->groupName))
    {
      return;
    }
    
    $this->log(array(
      'server'  => $_SERVER,
      'action'  => 'update',
      'type'    => 'config',
      'subject' => sprintf('%s = %s ( %s )', $setting->name, dmString::truncate($setting->value, 80), $event['culture'])
    ));
  }
  
  public function listenToSearchUpdatedEvent(sfEvent $event)
  {
    $this->log(array(
      'server'  => $_SERVER,
      'action'  => 'update',
      'type'    => 'search',
      'subject' => sprintf('"%s" updated: %d pages', $event['name'], $event['nb_documents'])
    ));
  }
  
  public function listenToSitemapUpdatedEvent(sfEvent $event)
  {
    $this->log(array(
      'server'  => $_SERVER,
      'action'  => 'update',
      'type'    => 'sitemap',
      'subject' => 'Sitemap updated'
    ));
  }
  
  public function listenToCacheClearEvent(sfEvent $event)
  {
    $this->log(array(
      'server'  => $_SERVER,
      'action'  => 'clear',
      'type'    => 'cache',
      'subject' => 'Cache cleared'
    ));
  }
  
  public function listenToUserSignInEvent(sfEvent $event)
  {
    $this->log(array(
      'server'  => $_SERVER,
      'action'  => 'sign_in',
      'type'    => 'user',
      'subject' => 'User logged in'
    ));
  }
  
  public function listenToUserSignOutEvent(sfEvent $event)
  {
    $this->log(array(
      'server'  => $_SERVER,
      'action'  => 'sign_out',
      'type'    => 'user',
      'subject' => 'User logged out'
    ));
  }
  
  public function listenToThrowExceptionEvent(sfEvent $event)
  {
    $this->log(array(
      'server'  => $_SERVER,
      'action'  => 'error',
      'type'    => 'exception',
      'subject' => $event->getSubject()->getMessage()
    ));
  }

  public function listenToRecordCreationEvent(sfEvent $event)
  {
    $record = $event->getSubject();

    if (!$this->isRecordAndActionLoggable($record, 'create'))
    {
      return;
    }

    list($subject, $type) = $this->getSubjectAndTypeFromRecord($record);

    $this->log(array(
      'server'  => $_SERVER,
      'action'  => 'create',
      'type'    => $type,
      'subject' => $subject,
      'record'  => $record
    ));
  }
  
  public function listenToRecordModificationEvent(sfEvent $event)
  {
    $action = $event['type'];

    if('create' == $action)
    {
      return;
    }

    $record = $event->getSubject();
    
    if (!$this->isRecordAndActionLoggable($record, $action))
    {
      return;
    }

    list($subject, $type) = $this->getSubjectAndTypeFromRecord($record, $action);
    
    $this->log(array(
      'server'  => $_SERVER,
      'action'  => $action,
      'type'    => $type,
      'subject' => $subject,
      'record'  => $action == 'delete' ? null : $record
    ));
  }

  protected function getSubjectAndTypeFromRecord($record)
  {
    try
    {
      $subject = $record->__toString();
    }
    catch(Exception $e)
    {
      $subject = '-';
    }

    if ($record instanceof DmPage)
    {
      $type = 'Page';
    }
    elseif ($record instanceof dmDoctrineRecord && $module = $record->getDmModule())
    {
      $type = $module->getName();
    }
    else
    {
      $type = get_class($record);
    }

    return array($subject, $type);
  }
  
  protected function isRecordAndActionLoggable($record, $action)
  {
    if(in_array(get_class($record), $this->options['ignore_models']))
    {
      return false;
    }
    
    if (!$this->options['ignore_internal_actions'])
    {
      return true;
    }
    
    if ($record instanceof DmPage)
    {
      return in_array($action, array('create', 'delete'));
    }
    
    if ($module = $record->getDmModule())
    {
      return $module->isProject();
    }
    
    return false;
  }
}