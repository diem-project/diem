<?php

class dmActionLog extends dmFileLog
{
  protected
  $defaults = array(
    'file'                => 'data/dm/log/action.log',
    'entry_service_name'  => 'action_log_entry',
    'ignore_models'       => 'DmError sfGuardRememberKey'
  );
  
  public function connect()
  {
    $this->serviceContainer->getService('dispatcher')->connect('dm.record.modification', array($this, 'listenToRecordModificationEvent'));
    
    $this->serviceContainer->getService('dispatcher')->connect('application.throw_exception', array($this, 'listenToThrowException'));
  }
  
  public function listenToThrowException(sfEvent $event)
  {
    $this->log(array(
      'server'  => $_SERVER,
      'user_id' => $this->serviceContainer->getService('user')->getGuardUserId(),
      'action'  => 'error',
      'type'    => 'exception',
      'subject' => $event->getSubject()->getMessage()
    ));
  }
  
  public function listenToRecordModificationEvent(sfEvent $event)
  {
    $record = $event->getSubject();
    
    if(in_array(get_class($record), explode(' ', $this->options['ignore_models'])))
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
      $type = dmModuleManager::getModule('dmPage')->getKey();
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
      'user_id' => $this->serviceContainer->getService('user')->getGuardUserId(),
      'action'  => $event['type'],
      'type'    => $type,
      'subject' => $subject
    ));
  }
}