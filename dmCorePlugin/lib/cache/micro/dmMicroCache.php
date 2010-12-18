<?php

abstract class dmMicroCache
{
  protected $cache = array();

  protected function getCache($key)
  {
    if(isset($this->cache[$key]))
    {
      return $this->cache[$key];
    }

    return null;
  }

  protected function hasCache($key)
  {
    return isset($this->cache[$key]);
  }

  protected function setCache($key, $value)
  {
    return $this->cache[$key] = $value;
  }

  public function clearCache($key = null)
  {
    if (null === $key)
    {
      $this->cache = array();
    }
    elseif(isset($this->cache[$key]))
    {
      unset($this->cache[$key]);
    }

    return $this;
  }
}