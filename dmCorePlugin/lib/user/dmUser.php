<?php

abstract class dmUser extends sfGuardSecurityUser implements dmMicroCacheInterface
{

	protected $cache = array();
	
	/*
	 * Guess user's browser
	 * @return dmBrowser browser object
	 */
	public function getBrowser()
	{
		if ($this->hasCache('browser'))
		{
			return $this->getCache('browser');
		}
		
		return $this->setCache('browser', dmBrowser::buildFromUserAgent($_SERVER['HTTP_USER_AGENT']));
	}
	
	/*
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
    if (is_null($cacheKey))
    {
      $this->cache = array();
    }
    elseif(isset($this->cache[$cacheKey]))
    {
      unset($this->cache[$cacheKey]);
    }

    return $this;
  }


  /*
   * Security methods
   */
  public function can($credentials)
	{
    if (!$this->isAuthenticated())
    {
      return false;
    }

    if ($this->getGuardUser()->isSuperAdmin)
    {
    	return true;
    }

    if (is_string($credentials))
    {
      if($this->hasCache('can '.$credentials))
      {
        return $this->getCache('can '.$credentials);
      }
    }

    $can = false;

    if (is_string($credentials))
    {
      $credentialsArray = explode(' ', $credentials);
    }
    else
    {
    	$credentialsArray = $credentials;
    }

    $can = count(array_intersect($credentialsArray, $this->getGuardUser()->getPermissionNames()));

    if (is_string($credentials))
    {
      $this->setCache('can '.$credentials, $can);
    }

    return $can;
	}

	/*
	 * Adds a value to a flash array
	 */
	public function addFlash($name, $value, $persist = true)
	{
		return $this->setFlash($name, array_unique(array_merge(
		  $this->getFlash($name, array()),
		  is_array($value) ? $value : array($value)
		)), $persist);
	}


	/*
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

  
  /*
   * Profile methods
   */
  public function getProfileId()
  {
    if ($profile = $this->getProfile())
    {
      return $profile->id;
    }
    
    return null;
  }

  public function getProfile()
  {
    if (!$this->isAuthenticated())
    {
      return null;
    }
    
    return $this->getGuardUser()->Profile;
  }

}