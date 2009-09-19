<?php

class dmActionLogEntry extends dmLogEntry
{
  protected static
  $browsersCache = array();
  
  public function configure(array $data)
  {
    $this->data = array(
      'time'          => $data['server']['REQUEST_TIME'],
      'app'           => sfConfig::get('sf_app'),
      'ip'            => $data['server']['REMOTE_ADDR'],
      'session_id'    => session_id(),
      'user_id'       => $data['user_id'],
      'user_agent'    => $data['server']['HTTP_USER_AGENT'],
      'message'       => $data['message']
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
  
  protected function getBrowser()
  {
    $hash = md5($this->get('user_agent'));
    
    if(!isset(self::$browsersCache[$hash]))
    {
      $browser = $this->serviceContainer->getService('browser');
      $browser->configureFromUserAgent($this->get('user_agent'));
      self::$browsersCache[$hash] = $browser;
    }
    
    return self::$browsersCache[$hash];
  }
  
}