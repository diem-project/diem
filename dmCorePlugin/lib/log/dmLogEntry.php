<?php

abstract class dmLogEntry
{
  protected
  $serviceContainer,
  $request,
  $data;
  
  public function __construct(sfServiceContainer $serviceContainer)
  {
    $this->serviceContainer = $serviceContainer;
    $this->request = $this->serviceContainer->getService('request');
  }
  
  abstract public function configure(array $data);

  public function get($key)
  {
    if(method_exists($this, $method = 'get'.dmString::camelize($key)))
    {
      return $this->$method();
    }
  
    if(isset($this->data[$key]))
    {
      return $this->data[$key];
    }
    
    return null;
  }
  
  public function setData($data)
  {
    $this->data = $data;
  }
  
  public function toArray()
  {
    return $this->data;
  }
  
  public function getIp()
  {
    if (!isset($this->data['ip']))
    {
      return null;
    }
    
    return '::1' === $this->data['ip'] ? 'localhost' : $this->data['ip'];
  }

  public function getCurrentRequestIp()
  {
    if(isset($_SERVER['REMOTE_ADDR']))
    {
      // localhost
      if(!$ip = $this->request->getForwardedFor())
      {
        $ip = $this->request->getRemoteAddress();
      }
      // proxies
      elseif(is_array($ip))
      {
        $ip = $ip[0];
      }
    }
    else
    {
      $ip = '-';
    }

    return $ip;
  }
}