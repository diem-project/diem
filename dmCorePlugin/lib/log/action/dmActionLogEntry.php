<?php

class dmActionLogEntry extends dmLogEntry
{
  protected static
  $usersCache     = array();
  
  public function configure(array $data)
  {
    $this->data = array(
      'time'          => $data['server']['REQUEST_TIME'],
      'ip'            => isset($data['server']['REMOTE_ADDR']) ? $data['server']['REMOTE_ADDR'] : '-',
      'session_id'    => session_id(),
      'user_id'       => $data['user_id'],
      'action'        => $data['action'],
      'subject'       => $data['subject']
    );
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