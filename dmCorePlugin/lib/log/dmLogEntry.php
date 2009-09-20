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
  
  public function setData($data)
  {
    $this->data = $data;
  }
  
  public function toArray()
  {
    return $this->data;
  }
}