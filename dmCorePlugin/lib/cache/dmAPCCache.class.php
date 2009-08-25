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
    return parent::set($key, serialize($data), $lifetime);
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
      self::$enabled = sfConfig::get("dm_cache_apc_enabled", true) && extension_loaded('apc');
    }

    return self::$enabled;
  }

}