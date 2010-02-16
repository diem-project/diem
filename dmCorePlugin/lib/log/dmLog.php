<?php

abstract class dmLog extends dmConfigurable
{
  public function getDefaultOptions()
  {
    return array(
      'key' => preg_replace('|^(\w+)Log$|', '$1', get_class($this)),
      'credentials' => 'see_log',
      'name' => get_class($this)
    );
  }
  
  public function initialize(array $options)
  {
    $this->configure($options);
  }
  
  public function getCredentials()
  {
    return $this->options['credentials'];
  }
  
  public function getKey()
  {
    return $this->options['key'];
  }
  
  public function getName()
  {
    return $this->options['name'];
  }
  
}