<?php
/**
 * File Cache Driver
 */
class Doctrine_Cache_Dm extends Doctrine_Cache_Driver
{
  protected
  $cache;

  /**
     * constructor
   *
     * @param array $options    associative array of cache driver options
   */
  public function __construct($options = array())
  {
    if (!isset($options['cache_manager']))
    {
      throw new dmException('Not supported yet');
    }
    
    parent::__construct($options);
  }

  public function getCache()
  {
    if (null === $this->cache)
    {
      $this->cache = $this->_options['cache_manager']->getCache('dm/doctrine');
    }
    return $this->cache;
  }

  /**
     * Fetch a cache record from this cache driver instance
   *
   * @param string $id cache id
   * @param boolean $testCacheValidity  if set to false, the cache validity won't be tested
   * @return string cached datas (or false)
   */
  public function fetch($id, $testCacheValidity = true)
  {
    if ($results = $this->getCache()->_get($this->_getKey($id)))
    {
      return $results;
    }
    else
    {
      return false;
    }
  }

  /**
     * Test if a cache record exists for the passed id
   *
   * @param string $id cache id
   * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
   */
  public function contains($id)
  {
    return $this->getCache()->has($this->_getKey($id));
  }

  /**
     * Save a cache record directly. This method is implemented by the cache
     * drivers and used in Doctrine_Cache_Driver::save()
   *
   * @param string $id        cache id
   * @param string $data      data to cache
   * @param int $lifeTime     if != false, set a specific lifetime for this cache record (null => infinite lifeTime)
   * @param boolean $saveKey  Whether or not to save the key in the cache key index
   * @return boolean true if no problem
   */
  public function saveCache($id, $data, $lifeTime = false)
  {
    return $this->getCache()->_set($id, $data, $lifeTime);
  }

  /**
     * Remove a cache record directly. This method is implemented by the cache
     * drivers and used in Doctrine_Cache_Driver::delete()
   *
   * @param string $id cache id
   * @return boolean true if no problem
   */
  public function deleteCache($id)
  {
    return $this->getCache()->remove($id);
  }
}