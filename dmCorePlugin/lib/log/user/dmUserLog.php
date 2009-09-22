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
    $this->serviceContainer->getService('dispatcher')->connect('dm.context.end', array($this, 'listenToContextEndEvent'));
  }
  
  public function listenToContextEndEvent(sfEvent $event)
  {
    $this->log(array(
      'context' => $this->serviceContainer->getService('context'),
      'server'  => $_SERVER
    ));
  }
}