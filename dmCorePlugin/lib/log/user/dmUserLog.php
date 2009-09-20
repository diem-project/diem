<?php

class dmUserLog extends dmFileLog
{
  protected
  $defaults = array(
    'file'                => 'data/dm/log/user.log',
    'entry_service_name'  => 'user_log_entry'
  );
  
  public function connect()
  {
    $this->serviceContainer->getService('dispatcher')->connect('dm.controller.end', array($this, 'listenToControllerEndEvent'));
  }
  
  public function listenToControllerEndEvent(sfEvent $event)
  {
    $this->log(array(
      'context' => $this->serviceContainer->getService('context'),
      'server'  => $_SERVER
    ));
  }
}