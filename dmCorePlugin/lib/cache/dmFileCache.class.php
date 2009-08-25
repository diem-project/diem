<?php

class dmFileCache extends sfFileCache
{

	public function initialize($options = array())
	{
		if (!isset($options['cache_dir']))
		{
			$options['cache_dir'] = dmOs::join(sfConfig::get("sf_cache_dir"), $options['prefix']);
		}

		return parent::initialize($options);
	}

  public function set($key, $data, $lifetime = null)
  {
    return $this->_set($key, serialize($data), $lifetime);
  }

  /*
   * will not serialize result
   */
  public function _set($key, $data, $lifetime = null)
  {
    return parent::set($key, $data, $lifetime);
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

//  public function clear($mode = sfCache::ALL)
//  {
//    if (is_dir($this->getOption("cache_dir")))
//    {
//      $this->removePattern("**");
//    }
//  }
}