<?php

class dmEventLogEntry extends dmLogEntry
{
  protected static
  $usersCache     = array();
  
  public function configure(array $data)
  {
    $userId = dmArray::get($data, 'user_id', $this->serviceContainer->getService('user')->getUserId());
    
    if (!$userId && dmConfig::isCli())
    {
      $userId = 'task';
    }
    
    $this->data = array(
      'time'          => (string) $data['server']['REQUEST_TIME'],
      'ip'            => (string) $this->getCurrentRequestIp(),
      'session_id'    => (string) session_id(),
      'user_id'       => (string) $userId,
      'action'        => (string) $data['action'],
      'type'          => (string) $data['type'],
      'subject'       => dmString::truncate($data['subject'], 500)
    );
  }
  
  public function isError()
  {
    return $this->data['type'] == 'exception';
  }
  
  protected function getUser()
  {
    $userId = $this->get('user_id');
    
    if($userId && is_numeric($userId))
    {
      if (!isset(self::$usersCache[$userId]))
      {
        self::$usersCache[$userId] = $userId ? dmDb::query('DmUser u')->where('u.id = ?', $userId)->fetchRecord() : null;
      }
      
      return self::$usersCache[$userId];
    }
    
    return null;
  }
  
  
  protected function getUsername()
  {
    return ($user = $this->getUser())
    ? $user->get('username')
    : ('task' === $this->get('user_id')
      ? 'task'
      : '');
  }
  
}