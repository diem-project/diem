<?php

class dmAction
{

  protected
    $key,
    $params;

  public function __construct($key, array $options)
  {
    $this->key = $key;

    $this->params = $options;
  }
  
  public function isCachable()
  {
    return $this->getParam('cache', false);
  }

  public function getParam($key, $default = null)
  {
    return isset($this->params[$key]) ? $this->params[$key] : $default;
  }

  public function setParam($key, $value)
  {
    return $this->params[$key] = $value;
  }

  public function getName()
  {
    return $this->getParam('name');
  }

  public function getType()
  {
    return $this->getParam('type');
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