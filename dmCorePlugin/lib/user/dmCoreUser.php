<?php

abstract class dmCoreUser extends dmSecurityUser implements dmMicroCacheInterface
{
  protected
  $browser,
  $cache = array();
  
  public function connect()
  {
    $this->dispatcher->connect('dm.context.loaded', array($this, 'listenToContextLoadedEvent'));
  }
  
  public function listenToContextLoadedEvent(sfEvent $e)
  {
    $this->setBrowser($e->getSubject()->get('browser'));
    
    if (!$e->getSubject()->getI18n()->cultureExists($this->getCulture()))
    {
      $this->setCulture(sfConfig::get('sf_default_culture'));
    }
  }
  
  /**
   * Guess user's browser
   * @return dmBrowser browser object
   */
  public function getBrowser()
  {
    return $this->browser;
  }
  
  public function setBrowser(dmBrowser $browser)
  {
    $this->browser = $browser;
  }

  /**
   * Will return the value only once per user session
   *
   * @param   mixed   $value the value to return only once
   * @param   string  $identifier an identifier to make this call unique
   * 
   * @return  mixed   $value or null
   */
  public function oncePerSession($value, $identifier = '')
  {
    $hash = md5($value.$identifier);

    if(!$this->hasAttribute('once.'.$hash))
    {
      $this->setAttribute('once.'.$hash, true);

      return $value;
    }
  }

  /**
   * Adds a value to a flash array
   */
  public function addFlash($name, $value, $persist = true)
  {
    return $this->setFlash($name, array_unique(array_merge(
      $this->getFlash($name, array()),
      is_array($value) ? $value : array($value)
    )), $persist);
  }
  
  /**
   * Log methods
   */
  public function logInfo($message, $persist = true)
  {
    return $this->addFlash('dm_log_info', $message, $persist);
  }

  public function logAlert($message, $persist = true)
  {
    return $this->addFlash('dm_log_alert', $message, $persist);
  }

  public function logError($message, $persist = true)
  {
    return $this->addFlash('dm_log_error', $message, $persist);
  }
  
  /**
   * Cache methods
   */
  public function getCache($cacheKey)
  {
    if(isset($this->cache[$cacheKey]))
    {
      return $this->cache[$cacheKey];
    }
    return null;
  }

  public function hasCache($cacheKey)
  {
    return isset($this->cache[$cacheKey]);
  }

  public function setCache($cacheKey, $cacheValue)
  {
    return $this->cache[$cacheKey] = $cacheValue;
  }

  public function clearCache($cacheKey = null)
  {
    if (null === $cacheKey)
    {
      $this->cache = array();
    }
    elseif(isset($this->cache[$cacheKey]))
    {
      unset($this->cache[$cacheKey]);
    }

    return $this;
  }
}
