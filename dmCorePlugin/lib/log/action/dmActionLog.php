<?php

class dmActionLog extends dmFileLog
{
  protected
  $defaults = array(
    'file'                => 'data/dm/log/action.log',
    'entry_service_name'  => 'action_log_entry'
  );
  
  public function connect()
  {
    $this->serviceContainer->getService('dispatcher')->connect('dm.record.modification', array($this, 'listenToRecordModificationEvent'));
    
    $this->serviceContainer->getService('dispatcher')->connect('dm.table.modification', array($this, 'listenToTableModificationEvent'));
  }
  
  public function listenToRecordModificationEvent(sfEvent $event)
  {
    $record = $event->getSubject();
    
    $this->log(array(
      'server'  => $_SERVER,
      'user_id' => $this->serviceContainer->getService('user')->getGuardUserId(),
      'message' => sprintf('modify record (%s) %s', get_class($record), $record->__toString())
    ));
  }
  
  public function listenToTableModificationEvent(sfEvent $event)
  {
    $table = $event->getSubject();
    
    $this->log(array(
      'server'  => $_SERVER,
      'user_id' => $this->serviceContainer->getService('user')->getGuardUserId(),
      'message' => sprintf('modify table %s', $table->getComponentName())
    ));
  }
}