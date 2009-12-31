<?php

class dmSecurityUser extends sfBasicSecurityUser
{
  protected
  $user = null,
  $isSuperAdmin = false;

  /**
   * Initializes the DmSecurityUser object.
   *
   * @param sfEventDispatcher $dispatcher The event dispatcher object
   * @param sfStorage $storage The session storage object
   * @param array $options An array of options
   */
  public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array())
  {
    parent::initialize($dispatcher, $storage, $options);

    if (!$this->isAuthenticated())
    {
      // remove user if timeout
      $this->getAttributeHolder()->removeNamespace('dmSecurityUser');
      $this->user = null;
    }
  }

  /**
   * Returns the referer uri.
   *
   * @param string $default The default uri to return
   * @return string $referer The referer
   */
  public function getReferer($default)
  {
    $referer = $this->getAttribute('referer', $default);
    $this->getAttributeHolder()->remove('referer');

    return $referer;
  }

  /**
   * Sets the referer.
   *
   * @param string $referer
   */
  public function setReferer($referer)
  {
    if (!$this->hasAttribute('referer'))
    {
      $this->setAttribute('referer', $referer);
    }
  }
  
  /**
   * Returns whether or not the user can do that
   *
   * @param string $credential The credential name
   * @return boolean
   */
  public function can($credentials)
  {
    if (empty($credentials))
    {
      return true;
    }
    elseif (!$this->getUser())
    {
      return false;
    }
    elseif($this->isSuperAdmin)
    {
      return true;
    }

    if(is_string($credentials))
    {
      $credentials = array_map('trim', explode(',', $credentials));
    }
    
    if(is_array($credentials))
    {
      return (bool) count(array_intersect($credentials, $this->credentials));
    }

    throw new dmException('Bad credentials : '.$credentials);
  }

  /**
   * Returns whether or not the user has the given credential.
   *
   * @param string $credential The credential name
   * @param boolean $useAnd Whether or not to use an AND condition
   * @return boolean
   */
  public function hasCredential($credential, $useAnd = true)
  {
    if (empty($credential))
    {
      return true;
    }

    if (!$this->getUser())
    {
      return false;
    }

    if ($this->isSuperAdmin)
    {
      return true;
    }

    return parent::hasCredential($credential, $useAnd);
  }

  /**
   * Returns whether or not the user is a super admin.
   *
   * @return boolean
   */
  public function isSuperAdmin()
  {
    return $this->isSuperAdmin;
  }
  
  public function getCacheHash()
  {
    return $this->getCulture().'_'.$this->getCredentialsHash();
  }
  
  public function getCredentialsHash()
  {
    return md5($this->isSuperAdmin.implode(',', $this->credentials));
  }

  /**
   * Returns whether or not the user is anonymous.
   *
   * @return boolean
   */
  public function isAnonymous()
  {
    return !$this->isAuthenticated();
  }

  /**
   * Signs in the user on the application.
   *
   * @param DmUser $user The DmUser id
   * @param boolean $remember Whether or not to remember the user
   * @param Doctrine_Connection $con A Doctrine_Connection object
   */
  public function signIn(DmUser $user, $remember = false, $con = null)
  {
    // signin
    $this->setAttribute('user_id', $user->get('id'), 'dmSecurityUser');
    $this->setAuthenticated(true);
    $this->clearCredentials();
    $this->addCredentials($user->getAllPermissionNames());

    // save last login
    $user->set('last_login', date('Y-m-d H:i:s'));
    $user->save($con);

    // remember?
    if ($remember)
    {
      $expirationAge = $this->getRememberKeyExpirationAge();

      // remove old keys
      Doctrine_Core::getTable('DmRememberKey')->createQuery()
        ->delete()
        ->where('created_at < ?', date('Y-m-d H:i:s', time() - $expirationAge))
        ->execute();

      // remove other keys from this user
      Doctrine_Core::getTable('DmRememberKey')->createQuery()
        ->delete()
        ->where('dm_user_id = ?', $user->getId())
        ->orWhere('ip_address = ?', $_SERVER['REMOTE_ADDR'])
        ->execute();

      // generate new keys
      $key = md5(dmString::random(20));

      // save key
      $rk = new DmRememberKey();
      $rk->setRememberKey($key);
      $rk->setUser($user);
      $rk->setIpAddress($_SERVER['REMOTE_ADDR']);
      $rk->save($con);

      $this->dispatcher->notify(new sfEvent($this, 'user.remember_me', array(
        'remember_key'    => $key,
        'expiration_age'  => $expirationAge
      )));
    }
    
    $this->dispatcher->notify(new sfEvent($this, 'user.sign_in'));
  }
  
  protected function getRememberKeyExpirationAge()
  {
    return sfConfig::get('dm_security_remember_key_expiration_age', 15 * 24 * 3600);
  }
  
  /**
   * Signs out the user.
   *
   */
  public function signOut()
  {
    $this->dispatcher->notify(new sfEvent($this, 'user.sign_out', array(
      'expiration_age' => $this->getRememberKeyExpirationAge()
    )));
    
    $this->setAttribute('user_id', null, 'dmSecurityUser');
    $this->getAttributeHolder()->removeNamespace('dmSecurityUser');
    $this->user = null;
    $this->clearCredentials();
    $this->setAuthenticated(false);
    $this->isSuperAdmin = false;
  }

  /**
   * Returns the related DmUser.
   *
   * @return DmUser
   */
  public function getUser()
  {
    if (!$this->user && ($id = $this->getAttribute('user_id', null, 'dmSecurityUser')))
    {
      $this->user = dmDb::table('DmUser')->findOneById($id);

      if (!$this->user)
      {
        // the user does not exist anymore in the database
        $this->signOut();

        throw new sfException('The user does not exist anymore in the database.');
      }
      
      $this->isSuperAdmin = $this->user->get('is_super_admin');
    }

    return $this->user;
  }

  public function getUserId()
  {
    if ($user = $this->getUser())
    {
      return $user->get('id');
    }
    
    return null;
  }

  /**
   * Returns the string representation of the object.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->getUser()->__toString();
  }

  /**
   * Returns the DmUser object's username.
   *
   * @return string
   */
  public function getUsername()
  {
    return $this->getUser()->get('username');
  }

  /**
   * Returns the DmUser object's email.
   *
   * @return string
   */
  public function getEmail()
  {
    return $this->getUser()->get('email');
  }

  /**
   * Sets the user's password.
   *
   * @param string $password The password
   * @param Doctrine_Collection $con A Doctrine_Connection object
   */
  public function setPassword($password, $con = null)
  {
    $this->getUser()->setPassword($password);
    $this->getUser()->save($con);
  }

  /**
   * Returns whether or not the given password is valid.
   *
   * @return boolean
   */
  public function checkPassword($password)
  {
    return $this->getUser()->checkPassword($password);
  }

  /**
   * Returns whether or not the user belongs to the given group.
   *
   * @param string $name The group name
   * @return boolean
   */
  public function hasGroup($name)
  {
    return $this->getUser() ? $this->getUser()->hasGroup($name) : false;
  }

  /**
   * Returns the user's groups.
   *
   * @return array|Doctrine_Collection
   */
  public function getGroups()
  {
    return $this->getUser() ? $this->getUser()->get('Groups') : array();
  }

  /**
   * Returns the user's group names.
   *
   * @return array
   */
  public function getGroupNames()
  {
    return $this->getUser() ? $this->getUser()->getGroupNames() : array();
  }

  /**
   * Returns whether or not the user has the given permission.
   *
   * @param string $name The permission name
   * @return string
   */
  public function hasPermission($name)
  {
    return $this->getUser() ? $this->getUser()->hasPermission($name) : false;
  }

  /**
   * Returns the Doctrine_Collection of single DmPermission objects.
   *
   * @return Doctrine_Collection
   */
  public function getPermissions()
  {
    return $this->getUser() ? $this->getUser()->get('Permissions') : array();
  }

  /**
   * Returns the array of permissions names.
   *
   * @return array
   */
  public function getPermissionNames()
  {
    return $this->getUser() ? $this->getUser()->getPermissionNames() : array();
  }

  /**
   * Returns the array of all permissions.
   *
   * @return array
   */
  public function getAllPermissions()
  {
    return $this->getUser() ? $this->getUser()->getAllPermissions() : array();
  }

  /**
   * Returns the array of all permissions names.
   *
   * @return array
   */
  public function getAllPermissionNames()
  {
    return $this->getUser() ? $this->getUser()->getAllPermissionNames() : array();
  }

  /**
   * Adds a group from its name to the current user.
   *
   * @param string $name The group name
   * @param Doctrine_Connection $con A Doctrine_Connection object
   */
  public function addGroupByName($name, $con = null)
  {
    return $this->getUser()->addGroupByName($name, $con);
  }

  /**
   * Adds a permission from its name to the current user.
   *
   * @param string $name The permission name
   * @param Doctrine_Connection $con A Doctrine_Connection object
   */
  public function addPermissionByName($name, $con = null)
  {
    return $this->getUser()->addPermissionByName($name, $con);
  }
}
