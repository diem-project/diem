<?php

class dmAPCCache extends sfAPCCache
{
  protected static
    $isApcEnabled;

  public function __construct($options = array())
  {
    if (isset($options['cache_dir']))
    {
      $name = substr($options['cache_dir'], strlen(sfConfig::get('sf_cache_dir'))+1);
    }
    elseif(isset($options['prefix']))
    {
      $name = dmProject::unRootify($options['prefix']);
    }
    else
    {
      throw new dmException('You must provide a cache_dir or a prefix');
    }
    
    $this->initialize(array(
      'prefix' => dmProject::getNormalizedRootDir().'/'.$name,
    ));
  }

  public function set($key, $data, $lifetime = null)
  {
    return parent::set($key, serialize($data), $lifetime);
  }

  public function _set($key, $data, $lifetime = null)
  {
    return parent::set($key, $data, $lifetime);
  }

  public function get($key, $default = null)
  {
    $data = parent::get($key, $default);

    if ($data != $default)
    {
      $data = unserialize($data);
    }

    return $data;
  }

  /**
   * will not unserialize result
   */
  public function _get($key, $default = null)
  {
    return parent::get($key, $default);
  }

  public function clear()
  {
    $infos = apc_cache_info('user');
    if (!is_array($infos['cache_list']))
    {
      return;
    }
    
    $prefix = $this->getOption('prefix');
    $prefixLength = strlen($prefix);
    
    foreach($infos['cache_list'] as $info)
    {
      if (strncmp($info['info'], $prefix, $prefixLength) === 0)
      {
        apc_delete($info['info']);
      }
    }
  }
  
  public static function isEnabled()
  {
    if (null === self::$isApcEnabled)
    {
      self::$isApcEnabled = sfConfig::get('dm_cache_apc', true) && self::isAvailable();
    }

    return self::$isApcEnabled;
  }
  
  public static function enable($val)
  {
    $val = (boolean) $val;
    
    if ($val && !self::isAvailable())
    {
      throw new dmException('Can not enable APC because it is not available on this environment');
    }
    else
    {
      if ($val != self::isEnabled())
      {
        self::clearAll();
      }
      
      self::$isApcEnabled = $val;
    }
  }
  
  public static function isAvailable()
  {
    return function_exists('apc_store') && ini_get('apc.enabled');
  }

  public static function clearAll()
  {
    // clear opcode cache leads to strange behaviors...
//    apc_clear_cache('opcode');
    return apc_clear_cache('user');
  }

  public static function getLoad()
  {
    if (!self::isAvailable())
    {
      throw new dmException('APC is not available on this environment');
    }
    
    try
    {
      $infos = apc_sma_info(true);
      $total = $infos['seg_size'];
      $free = $infos['avail_mem'];
    }
    catch(RuntimeException $e)
    {
      if(sfConfig::get('dm_debug'))
      {
        throw $e;
      }
      
      $total = 1;
      $free = 1;
    }
    
    return array(
      'usage'   => round(($total - $free) / (1024*1024)).'Mo',
      'limit'   => round($total / (1024*1024)).'Mo',
      'percent' => min(100, (($total - $free) / $total) * 100)
    );
  }

}