<?php

class dmUserLogEntry extends dmMicroCache
{
	protected static
	$browsersCache = array(),
	$usersCache    = array();
	
	protected
	$data;
	
	public function __construct(array $data)
	{
		$this->data = $data;
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
  	return ($user = $this->getUser()) ? $user->username : null;
  }
	
	protected function getBrowser()
	{
		$hash = md5($this->get('user_agent'));
		
		if(!isset(self::$browsersCache[$hash]))
		{
			self::$browsersCache[$hash] = dmBrowser::buildFromUserAgent($this->get('user_agent'));
		}
		
		return self::$browsersCache[$hash];
	}
	
	protected function getIsOk()
	{
		return in_array($this->get('code'), array(200));
	}
	
	public function get($key)
	{
		if(isset($this->data[$key]))
		{
			return $this->data[$key];
		}
		
		if(method_exists($this, $method = 'get'.dmString::camelize($key)))
		{
			return $this->$method();
		}
		return null;
	}
	
	public function toJson()
	{
		return str_replace('\/', '/', json_encode($this->toArray()));
	}
	
	public function toArray()
	{
		return $this->data;
	}
	
	public static function createFromJson($json)
	{
		return new self(json_decode($json, true));
	}
	
	public static function createFromDmContext(dmContext $dmContext)
	{
    return new self(array(
      'uri'           => isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'], '/') : '/',
      'code'          => $dmContext->getSfContext()->getResponse()->getStatusCode(),
      'app'           => sfConfig::get('sf_app'),
      'time'          => $_SERVER['REQUEST_TIME'],
      'ip'            => $_SERVER['REMOTE_ADDR'],
      'session_id'    => session_id(),
      'user_id'       => $dmContext->getSfContext()->getUser()->getGuardUserId(),
      'user_agent'    => $_SERVER['HTTP_USER_AGENT'],
      'timer'         => sprintf('%.0f', (microtime(true) - dm::getStartTime()) * 1000)
    ));
	}
}