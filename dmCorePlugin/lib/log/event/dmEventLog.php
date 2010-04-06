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
    'subject'
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
      'application.throw_exception' => 'listenToThrowExceptionEvent',
      'user.sign_in' => 'listenToUserSignInEvent',
      'user.sign_out' => 'listenToUserSignOutEvent',
      'dm.cache.clear' => 'listenToCacheClearEvent',
      'dm.sitemap.generated' => 'listenToSitemapUpdatedEvent',
      'dm.search.populated' => 'listenToSearchUpdatedEvent',
      'dm.config.updated' => 'listenToConfigUpdatedEvent'
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
  
  public function listenToRecordModificationEvent(sfEvent $event)
  {
    $record = $event->getSubject();
    $action = $event['type'];
    
    if (!$this->isRecordActionLoggable($record, $action))
    {
      return;
    }
    
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
    
    $this->log(array(
      'server'  => $_SERVER,
      'action'  => $action,
      'type'    => $type,
      'subject' => $subject
    ));
  }
  
  protected function isRecordActionLoggable($record, $action)
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