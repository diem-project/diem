<?php

class dmActionLogEntry extends dmLogEntry
{
  protected static
  $usersCache     = array();
  
  public function configure(array $data)
  {
    $this->data = array(
      'time'          => (string) $data['server']['REQUEST_TIME'],
      'ip'            => (string) isset($data['server']['REMOTE_ADDR']) ? $data['server']['REMOTE_ADDR'] : '-',
      'session_id'    => (string) session_id(),
      'user_id'       => (string) $data['user_id'],
      'action'        => (string) $data['action'],
      'type'          => (string) $data['type'],
      'subject'       => (string) $data['subject']
    );
  }
  
  public function isError()
  {
    return $this->data['type'] == 'exception';
  }
  
  protected function getUser()
  {
    $userId = $this->get('user_id');
    
    if(!isset(self::$usersCache[$userId]))
    {
      self::$usersCache[$userId] = $userId ? dmDb::query('sfGuardUser u')->where('u.id = ?', $userId)->fetchRecord() : null;
    }
    
    return self::$usersCache[$userId];
  }
  
  
  protected function getUsername()
  {
    return ($user = $this->getUser()) ? $user->get('username') : null;
  }
  
}