<?php

class dmAction extends dmConfigurable
{
  protected
    $key;

  public function __construct($key, array $options)
  {
    $this->key = $key;

    $this->configure($options);
  }
  
  public function isCachable()
  {
    return $this->getOption('cache', false);
  }

  public function getName()
  {
    return $this->getOption('name');
  }

  public function getType()
  {
    return $this->getOption('type');
  }

  public function getKey()
  {
    return $this->key;
  }

  public function getUnderscore()
  {
    return dmString::underscore($this->getKey());
  }

  public function __toString()
  {
    return $this->getKey();
  }
}