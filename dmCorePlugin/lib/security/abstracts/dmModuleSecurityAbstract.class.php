<?php
/*
 *
 *
 */

/**
 *
 * @author serard
 *
 */
class dmModuleSecurityAbstract extends dmMetaCache
{

  /**
   * @var dmContext
   */
  protected $context;

  /**
   * @var sfServiceContainer
   */
  protected $container;
  
  /**
   * @var dmUser
   */
  protected $user;

  /**
   * Constructor.
   * This method is used to construct a child class instance,
   * and helps sfServiceContainer injects dependancies
   *
   * @param dmContext $context
   * @param sfServiceContainer $container
   */
  public function __construct(dmContext $context, sfServiceContainer $container, $user)
  {
    $this->context = $context;
    $this->container = $container;
    $this->user = $user;
    
    $this->initialize(array('cache_dir' => dmOs::join(sfConfig::get('sf_cache_dir'), $this->getApplication(), 'security')));
  }

  /**
   * Returns the current application (admin|front for example).
   *
   * @return string
   */
  public function getApplication()
  {
    return $this->context->getConfiguration()->getApplication();
  }
  
  public function get($key, $default = null)
  {
  	return parent::get($this->getMD5() . $key, $default);
  }
  
  public function set($key, $value, $lifetime = null)
  {
  	parent::set($this->getMD5() . $key, $value, $lifetime);
  	return $this;
  }
  
  public function has($key)
  {
  	return parent::has($this->getMD5() . $key);
  }
  
  public function remove($key)
  {
  	parent::remove($this->getMD5() . $key);
  	return $this;
  }
  
  public function getMD5()
  {
  	return md5($this->getApplication() . ($this->user->isAuthenticated() ? serialize($this->user->getUser()->toArray(false)) : 0));
  }
}