<?php

class dmUserLogEntry extends dmLogEntry
{
  protected static
  $browsersCache = array(),
  $usersCache    = array();
  
  public function configure(array $data)
  {
    $this->data = array(
      'time'          => (string) $data['server']['REQUEST_TIME'],
      'uri'           => (string) isset($data['server']['PATH_INFO']) ? trim($data['server']['PATH_INFO'], '/') : '/',
      'code'          => (string) $data['context']->getResponse()->getStatusCode(),
      'app'           => (string) sfConfig::get('sf_app'),
      'ip'            => (string) $data['server']['REMOTE_ADDR'],
      'session_id'    => (string) session_id(),
      'user_id'       => (string) $data['context']->getUser()->getGuardUserId(),
      'user_agent'    => (string) $data['server']['HTTP_USER_AGENT'],
      'timer'         => (string) sprintf('%.0f', (microtime(true) - dm::getStartTime()) * 1000)
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
  
  protected function getIsOk()
  {
    return in_array($this->get('code'), array(200));
  }
  
}