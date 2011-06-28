<?php

class dmMetaCache extends sfCache
{
  protected
    $cache;

  public function initialize($options = array())
  {
    $cacheClass = $this->getCurrentCacheClass();
    
    $this->cache = new $cacheClass($options);
  }
  
  public function getCurrentCacheClass()
  {
    return dmAPCCache::isEnabled() ? 'dmAPCCache' : 'dmFileCache';
  }

  public function getCache()
  {
    return $this->cache;
  }

  public function get($key, $default = null)
  {
    return $this->cache->get($key, $default);
  }

  /**
   * will not unserialize result
   */
  public function _get($key, $default = null)
  {
    return $this->cache->_get($key, $default);
  }

  public function has($key)
  {
    return $this->cache->has($key);
  }

  public function set($key, $data, $lifetime = null)
  {
    return $this->cache->set($key, $data, $lifetime);
  }

  /**
   * will not serialize result
   */
  public function _set($key, $data, $lifetime = null)
  {
    return $this->cache->_set($key, $data, $lifetime);
  }

  public function remove($key)
  {
    return $this->cache->remove($key);
  }

  public function removePattern($pattern)
  {
    return $this->cache->removePattern($pattern);
  }

  public function clean($mode = self::ALL)
  {
    return $this->cache->clean($mode);
  }
  
  public function clear()
  {
    return $this->cache->clear();
  }

  public function getTimeout($key)
  {
    return $this->getTimeout($key);
  }

  public function getLastModified($key)
  {
    return $this->cache->getLastModified($key);
  }

}