<?php

class dmUserLogEntry extends dmMicroCache
{
	protected static
	$browsersCache = array();
	
	protected
	$data;
	
	public function __construct(array $data)
	{
		$this->data = $data;
	}
	
	public function getBrowser()
	{
		$hash = md5($this->get('user_agent'));
		
		if(!isset(self::$browsersCache[$hash]))
		{
			self::$browsersCache[$hash] = dmBrowser::buildFromUserAgent($this->get('user_agent'));
		}
		
		return self::$browsersCache[$hash];
	}
	
	public function get($key)
	{
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}
	
	public function toJson()
	{
		return json_encode(str_replace('\/', '/', $this->toArray()));
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
      'uri'           => trim($_SERVER['PATH_INFO'], '/'),
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