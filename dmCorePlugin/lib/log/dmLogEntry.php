<?php

abstract class dmLogEntry
{
  protected
  $serviceContainer,
  $data;
  
  public function __construct(sfServiceContainer $serviceContainer)
  {
    $this->serviceContainer = $serviceContainer;
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
}