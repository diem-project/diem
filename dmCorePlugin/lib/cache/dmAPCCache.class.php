<?php

class dmAPCCache extends sfAPCCache
{
	protected static
	  $enabled;

  public function __construct($name)
  {
    $this->initialize(array(
      "prefix" => dmProject::getKey()."/".$name,
    ));
  }

  public function set($key, $data, $lifetime = null)
  {
    return $this->_set($key, serialize($data), $lifetime);
  }

  public function _set($key, $data, $lifetime = null)
  {
    return parent::set($key, serialize($data), $lifetime);
  }

  public function get($key, $default = null)
  {
    $data = $this->_get($key, $default);

    if ($data != $default)
    {
      $data = unserialize($data);
    }

    return $data;
  }

  /*
   * will not unserialize result
   */
  public function _get($key, $default = null)
  {
    return parent::get($key, $default);
  }

  public function clear()
  {
    $this->removePattern("**");
    //aze::debug();
    // cache systeme puis user
    //apc_clear_cache('user');
    //aze::debug(apc_cache_info("user"));
  }

  public static function isEnabled($val = null)
  {
    if ($val !== null)
    {
      self::$enabled = extension_loaded('apc') && $val;
      dmCacheManager::getInstance()->reset();
    }

    if (self::$enabled === null)
    {
      self::$enabled = sfConfig::get("dm_cache_apc", true) && extension_loaded('apc');
    }

    return self::$enabled;
  }

}