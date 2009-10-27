<?php

class dmRequestLogEntry extends dmLogEntry
{
  const MEM_ALERT   = 33554432;  // 32 Mb
  const TIME_ALERT  = 2000;      // 2 s
  
  protected static
  $browsersCache = array(),
  $usersCache    = array();
  
  public function configure(array $data)
  {
    $isXhr = $data['context']->getRequest()->isXmlHttpRequest();
    
    $uri = $this->cleanUri(dmArray::get($data['server'], 'PATH_INFO'));
    
    $this->data = array(
      'time'          => (string) $data['server']['REQUEST_TIME'],
      'uri'           => (string) $uri,
      'code'          => (string) $data['context']->getResponse()->getStatusCode(),
      'app'           => (string) sfConfig::get('sf_app'),
      'env'           => (string) sfConfig::get('sf_environment'),
      'ip'            => (string) $data['server']['REMOTE_ADDR'],
      'user_id'       => (string) $data['context']->getUser()->getGuardUserId(),
      'user_agent'    => (string) $isXhr ? null : $data['server']['HTTP_USER_AGENT'],
      'xhr'           => (int)    $isXhr,
      'mem'           => (string) memory_get_peak_usage(true),
      'timer'         => (string) sprintf('%.0f', (microtime(true) - dm::getStartTime()) * 1000)
    );
  }
  
  protected function cleanUri($uri)
  {
    if (strpos($uri, '?_='))
    {
      $cleanUri = preg_replace('|(.+)(?:\?_=\d+)(.*)|', '$1$2', $uri);
      
      if ($firstAmp = strpos($cleanUri, '&'))
      {
        $cleanUri{$firstAmp} = '?';
      }
    }
    else
    {
      $cleanUri = $uri;
    }
    
    return '' === $cleanUri ? '/' : $cleanUri;
  }
  
  public function getUser()
  {
    $userId = $this->get('user_id');
    
    if(!isset(self::$usersCache[$userId]))
    {
      self::$usersCache[$userId] = $userId ? dmDb::query('sfGuardUser u')->where('u.id = ?', $userId)->fetchRecord() : null;
    }
    
    return self::$usersCache[$userId];
  }
  
  
  public function getUsername()
  {
    return ($user = $this->getUser()) ? $user->get('username') : null;
  }
  
  public function getBrowser()
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
  
  public function renderCodeOrNull()
  {
    return 200 == $this->get('code') ? '' : $this->get('code');
  }
  
  public static function isError(array $data)
  {
    return !in_array($data['code'], array(200, 301, 302));
  }
  
  public static function isAlert(array $data)
  {
    return $data['timer'] > self::TIME_ALERT || $data['mem'] > self::MEM_ALERT;
  }
  
  public function getStatus()
  {
    return self::isError($this->data) ? 'busy' : (self::isAlert($this->data) ? 'away' : 'ok');
  }
  
}