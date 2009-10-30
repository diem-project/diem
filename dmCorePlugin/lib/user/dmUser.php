<?php

abstract class dmUser extends sfGuardSecurityUser implements dmMicroCacheInterface
{

  protected
  $browser,
  $isBrowserConfigured,
  $isSuperAdmin = null,
  $cache = array();
  
  public function connect()
  {
    $this->dispatcher->connect('dm.context.loaded', array($this, 'listenToContextLoadedEvent'));
  }
  
  public function listenToContextLoadedEvent(sfEvent $e)
  {
    $this->setBrowser($e->getSubject()->get('browser'));
  }
  
  public function setCulture($culture)
  {
    if (!in_array($culture, sfConfig::get('dm_i18n_cultures')))
    {
      throw new dmException(sprintf('%s is not a valid culture defined in dm_i18n_cultures', $culture));
    }
    
    return parent::setCulture($culture);
  }
  
  /*
   * Guess user's browser
   * @return dmBrowser browser object
   */
  public function getBrowser()
  {
    if (!$this->isBrowserConfigured)
    {
      $this->browser->configureFromUserAgent($_SERVER['HTTP_USER_AGENT']);
      $this->isBrowserConfigured = true;
    }
    
    return $this->browser;
  }
  
  public function setBrowser(dmBrowser $browser)
  {
    $this->browser = $browser;
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


  public function setReferer($referer)
  {
    if (!$this->hasAttribute('referer'))
    {
      $this->setAttribute('referer', $referer);
    }
  }
  
  /*
   * Security methods
   */
  public function hasCredential($credential, $useAnd = true)
  {
    if (empty($credential))
    {
      return true;
    }

    if (!$this->getGuardUser())
    {
      return false;
    }

    if ($this->isSuperAdmin())
    {
      return true;
    }

    return sfBasicSecurityUser::hasCredential($credential, $useAnd);
  }
  
  public function can($credentials)
  {
    if (!$this->getGuardUser())
    {
      $can = false;
    }
    elseif (empty($credentials))
    {
      $can = true;
    }
    elseif($this->isSuperAdmin)
    {
      $can = true;
    }
    elseif(is_string($credentials))
    {
      $can = in_array($credentials, $this->credentials);
    }
    elseif(is_array($credentials))
    {
      $can = count(array_intersect($credentials, $this->credentials));
    }
    else
    {
      throw new dmException('Bad credentials : '.$credentials);
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

  public function signOut()
  {
    $this->dispatcher->notify(new sfEvent($this, 'user.sign_out'));
    
    parent::signOut();
    
    $this->isSuperAdmin = false;
  }
  
  public function signIn($user, $remember = false, $con = null)
  {
    parent::signIn($user, $remember, $con);
    
    $this->dispatcher->notify(new sfEvent($this, 'user.sign_in'));
  }

  public function getGuardUser()
  {
    if (!$this->user && $id = $this->getAttribute('user_id', null, 'sfGuardSecurityUser'))
    {
      $this->user = dmDb::query('sfGuardUser s')->where('s.id = ?', $id)->fetchRecord();
      
      if (!$this->user)
      {
        // the user does not exist anymore in the database
        $this->signOut();

        throw new sfException('The user does not exist anymore in the database. Please reload the page and login.');
      }
      
      $this->isSuperAdmin = $this->user->get('is_super_admin');
    }

    return $this->user;
  }

  public function isSuperAdmin()
  {
    return $this->isSuperAdmin;
  }
  
  public function getCredentialsHash()
  {
    return md5($this->isSuperAdmin.implode(',', $this->credentials));
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
  
  public function getGuardUserId()
  {
    if ($guardUser = $this->getGuardUser())
    {
      return $guardUser->get('id');
    }
    
    return null;
  }

}