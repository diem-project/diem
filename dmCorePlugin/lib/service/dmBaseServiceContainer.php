<?php

abstract class dmBaseServiceContainer extends sfServiceContainer
{
  protected
  $options = array();
  
  public function configure(array $dependencies, array $options = array())
  {
    $this->options = array_merge($this->options, $options);
    
    $this->loadDependencies($dependencies);
    
    $this->loadParameters();
    
    $this->configureServices();
  }
  
  protected function loadDependencies(array $dependencies)
  {
    $this->setService('dispatcher',       $dependencies['context']->getEventDispatcher());
    $this->setService('user',             $dependencies['context']->getUser());
    $this->setService('response',         $dependencies['context']->getResponse());
    $this->setService('i18n',             $dependencies['context']->getI18n());
    $this->setService('logger',           $dependencies['context']->getLogger());
    $this->setService('config_cache',     $dependencies['context']->getConfigCache());
    $this->setService('controller',       $dependencies['context']->getController());
    $this->setService('request',          $dependencies['context']->getRequest());
    $this->setService('module_manager',   $dependencies['context']->getModuleManager());
    $this->setService('context',          $dependencies['context']);
    $this->setService('doctrine_manager', $dependencies['doctrine_manager']);
  }
  
  protected function loadParameters()
  {
    $this->setParameter('request.context',  $this->getService('request')->getRequestContext());
    
    $this->setParameter('user.culture',     $this->getService('user')->getCulture());
  }
  
  protected function configureServices()
  {
    $this->configureUser();
    
    $this->configureResponse();
  }
  
  protected function configureUser()
  {
    $this->getService('user')->setBrowser($this->getService('browser'));
  }
  
  protected function configureResponse()
  {
    $response = $this->getService('response');
    
    if ($response->isHtmlForHuman())
    {
      $response->setAssetConfig($this->getService('asset_config'));
      
      $userCanCodeEditor = $this->getService('user')->can('code_editor');
      
      $this->getService('stylesheet_compressor')->setOption('protect_user_assets', $userCanCodeEditor);
  
      $this->getService('javascript_compressor')->setOption('protect_user_assets', $userCanCodeEditor);
    }
  }
  
  
  public function connect()
  {
    $dispatcher = $this->getService('dispatcher');
    
    $dispatcher->connect('user.change_culture', array($this, 'listenToChangeCultureEvent'));
    
    $dispatcher->connect('user.change_theme', array($this, 'listenToChangeThemeEvent'));
    
    $dispatcher->connect('controller.change_action', array($this, 'listenToChangeActionEvent'));
    
    $this->connectServices();
  }
  
  protected function connectServices()
  {
    if (!dmConfig::isCli())
    {
      /*
       * Connect the tree watcher to make it aware of database modifications
       */
      $this->getService('page_tree_watcher')->connect();
      
      /*
       * Connect the cache cleaner
       */
      $this->getService('cache_cleaner')->connect();
    }
    
    if ('test' != sfConfig::get('sf_environment'))
    {
      /*
       * Connect the error watcher to make it aware of thrown exceptions
       */
      $this->getService('error_watcher')->connect();
      
      /*
       * Connect the event log to make it aware of database modifications
       */
      $this->getService('event_log')->connect();
      
      /*
       * Connect the request log to make it aware of controller end
       */
      $this->getService('request_log')->connect();
    }
    
    if ($this->getService('response')->isHtmlForHuman())
    {
      /*
       * Enable stylesheet compression
       */
      $this->getService('stylesheet_compressor')->connect();
      
      /*
       * Enable javascript compression
       */
      $this->getService('javascript_compressor')->connect();
    }
    
    $this->getService('user')->connect();
    
    /*
     * Enable page i18n builder for multilingual sites
     */
    $cultures = $this->getService('i18n')->getCultures();
    if (count($cultures) > 1)
    {
      $this->mergeParameter('page_i18n_builder.options', array(
        'cultures' => $cultures
      ));
      
      $this->getService('page_i18n_builder')->connect();
    }
  }

  /**
   * Listens to the user.change_culture event.
   *
   * @param sfEvent An sfEvent instance
   */
  public function listenToChangeCultureEvent(sfEvent $event)
  {
    $this->setParameter('user.culture', $event['culture']);
  }
  
  /**
   * Listens to the user.change_theme event.
   *
   * @param sfEvent An sfEvent instance
   */
  public function listenToChangeThemeEvent(sfEvent $event)
  {
    $this->setParameter('user.theme', $event['theme']);
  }
  /**
   * Listens to the controller.change_action event.
   *
   * @param sfEvent An sfEvent instance
   */
  public function listenToChangeActionEvent(sfEvent $event)
  {
    $this->setParameter('controller.module', $event['module']);
    $this->setParameter('controller.action', $event['action']);
  }
  
  
  /*
   * Compatibility with sfContext
   */
  public function get($name)
  {
    return $this->getService($name);
  }
  
  /**
   * Merges a service container parameter.
   *
   * @param string $name       The parameter name
   * @param mixed  $parameters The parameter value
   */
  public function mergeParameter($name, $value)
  {
    $name = strtolower($name);
    
    $this->parameters[$name] = array_merge($this->parameters[$name], $value);
    
    return $this;
  }
  
  
  public function reload($id)
  {
    if (!$this->hasService($id))
    {
      throw new InvalidArgumentException(sprintf('The service "%s" does not exist.', $id));
    }
    
    if(isset($this->shared[$id]))
    {
      unset($this->shared[$id]);
    }
    
    return $this;
  }
  
  
  /**
   * Sets a service container parameter.
   *
   * @param string $name       The parameter name
   * @param mixed  $parameters The parameter value
   * @return dmBaseServiceContainer $this this instance
   */
  public function setParameter($name, $value)
  {
    parent::setParameter($name, $value);
    
    return $this;
  }
  
  /**
   * Returns true if the given service is defined.
   *
   * @param  string  $id      The service identifier
   *
   * @return Boolean true if the service is defined, false otherwise
   */
  public function hasService($id)
  {
    return isset($this->services[$id]) || (!empty($id) && method_exists($this, 'get'.dmString::camelize($id).'Service'));
  }

  /**
   * Gets a service.
   *
   * If a service is both defined through a setService() method and
   * with a set*Service() method, the former has always precedence.
   *
   * @param  string $id The service identifier
   *
   * @return object The associated service
   *
   * @throw InvalidArgumentException if the service is not defined
   */
  public function getService($id)
  {
    if (isset($this->services[$id]))
    {
      return $this->services[$id];
    }
    
    if (!empty($id) && method_exists($this, $method = 'get'.dmString::camelize($id).'Service'))
    {
      return $this->$method();
    }

    throw new InvalidArgumentException(sprintf('The service "%s" does not exist.', $id));
  }
  
}