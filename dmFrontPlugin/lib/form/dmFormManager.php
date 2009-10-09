<?php

class dmFormManager
{
  protected
  $forms;
  
  public function __construct()
  {
    $this->initialize();
  }
  
  public function initialize()
  {
    $this->forms = array();
  }
  
  public function set($key, dmForm $form)
  {
    $this->forms[$key] = $form;
  }
  
  public function get($key)
  {
    if (!$this->has($key))
    {
      throw new dmException('Form '.$key.' does not exist');
    }
    
    return $this->forms[$key];
  }
  
  public function has($key)
  {
    return isset($this->forms[$key]);
  }
}