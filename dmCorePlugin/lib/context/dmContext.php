<?php

class dmContext extends sfContext
{

  protected
  $serviceContainer,
  $dmConfiguration,
  $page,
  $isHtmlForHuman;
  
  /**
   * Creates a new context instance.
   *
   * @param  sfApplicationConfiguration $configuration  An sfApplicationConfiguration instance
   * @param  string                     $name           A name for this context (application name by default)
   * @param  string                     $class          The context class to use (dmContext by default)
   *
   * @return dmContext                  A dmContext instance
   */
  public static function createInstance(sfApplicationConfiguration $configuration, $name = null, $class = __CLASS__)
  {
    return parent::createInstance($configuration, $name, $class);
  }
  
  /**
   * Initializes the current dmContext instance.
   *
   * @param dmApplicationConfiguration $configuration  An dmApplicationConfiguration instance
   */
  public function initialize(sfApplicationConfiguration $configuration)
  {
    parent::initialize($configuration);
    
    $timer = dmDebug::timerOrNull('dmContext::initialize');
    
    sfConfig::set('dm_debug', $this->getRequest()->getParameter('dm_debug', false));
    
    // load the service container instance
    $this->loadServiceContainer();
    
    // configure the service container with its required dependencies
    $this->configureServiceContainer($this->serviceContainer);
    
    // connect the service container and its services to the event dispatcher
    $this->serviceContainer->connect();

    /*
     * dmHtmlTag requires service container to create link and media tags
     */
    dmHtmlTag::setContext($this);
    
    /*
     * dmForm requires service container...
     */
    dmForm::setServiceContainer($this->serviceContainer);
    
    /*
     * dmDoctrineRecord needs the event dispatcher to communicate
     * and the service container...
     */
    dmDoctrineRecord::setEventDispatcher($this->dispatcher);
    dmDoctrineRecord::setServiceContainer($this->serviceContainer);

    /*
     * Doctrine cache configuration require a loaded dmContext to run
     */
    $this->serviceContainer->getService('doctrine_config')->configureCache();

    // notify that context is ready
    $this->dispatcher->notify(new sfEvent($this, 'dm.context.loaded'));
    
    $timer && $timer->addTime();
  }

  /**
   * Gets an object from the current context.
   *
   * @param  string $name  The name of the object to retrieve
   *
   * @return object The object associated with the given name
   */
  public function get($name)
  {
    if (isset($this->factories[$name]))
    {
      return $this->factories[$name];
    }
    
    if($this->serviceContainer->hasService($name))
    {
      return $this->serviceContainer->getService($name);
    }

    throw new sfException(sprintf('The "%s" object does not exist in the current context.', $name));
  }
  
  /*
   * Loads the diem services
   */
  protected function loadServiceContainer()
  {
    require_once(sfConfig::get('dm_core_dir').'/lib/vendor/sfService/sfServiceContainerInterface.php');
    require_once(sfConfig::get('dm_core_dir').'/lib/vendor/sfService/sfServiceContainer.php');

    $name = 'dm'.dmString::camelize(sfConfig::get('sf_app')).'ServiceContainer';
    $file = dmOs::join(sfConfig::get('dm_cache_dir'), 'services', $name.'.php');
     
    if (!file_exists($file))
    {
      $this->dumpServiceContainer($name, $file);
    }
    
    require_once($file);
    $this->serviceContainer = new $name;
  }

  public function configureServiceContainer(dmBaseServiceContainer $serviceContainer)
  {
    $serviceContainer->configure(
    array(
      'context'           => $this,
      'doctrine_manager'  => Doctrine_Manager::getInstance()
    ),
    array(
      'human'             => $this->isHtmlForHuman()
    ));
  }
  
  protected function dumpServiceContainer($name, $file)
  {
    foreach(array(sfConfig::get('dm_cache_dir'), dirname($file)) as $dir)
    {
      if (!is_dir($dir))
      {
        $oldUmask = umask(0);
        mkdir($dir, 0777);
        umask($oldUmask);
      }
    }

    $this->loadServiceContainerExtraStuff();

    $sc = new sfServiceContainerBuilder;

    $loader = new sfServiceContainerLoaderFileYaml($sc);
    $loader->load($this->configuration->getConfigPaths('config/dm/services.yml'));

    $dumper = new sfServiceContainerDumperPhp($sc);
    $baseClass = sfConfig::get('dm_service_container_base_class', 'dm'.ucfirst(sfConfig::get('dm_context_type')).'BaseServiceContainer');
    
    file_put_contents($file, $dumper->dump(array('class' => $name, 'base_class' => $baseClass)));
    chmod($file, 0777);
    
    if(!file_exists($file))
    {
      throw new dmException('Can not write the generated service container to '.$file);
    }
    
    unset($dumper, $loader, $sc);
  }

  /*
   * Load the required classes to load a service container from yml configuration
   */
  public function loadServiceContainerExtraStuff()
  {
    $loaderFilePrefix = dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/sfService/sfService');

    foreach(array('Definition', 'Reference', 'ContainerBuilder', 'ContainerLoaderInterface', 'ContainerLoader', 'ContainerLoaderFile', 'ContainerLoaderFileYaml', 'ContainerDumperInterface', 'ContainerDumper', 'ContainerDumperPhp') as $requiredClassSuffix)
    {
      require_once($loaderFilePrefix.$requiredClassSuffix.'.php');
    }
  }

  /*
   * @return sfServiceContainer
   */
  public function getServiceContainer()
  {
    return $this->serviceContainer;
  }

  /*
   * @return dmCacheManager
   */
  public function getCacheManager()
  {
    return $this->serviceContainer->getService('cache_manager');
  }

  /*
   * @return dmFilesystem
   */
  public function getFilesystem()
  {
    return $this->serviceContainer->getService('filesystem');
  }

  /*
   * @return dmHelper
   */
  public function getHelper()
  {
    return $this->serviceContainer->getService('helper');
  }

  /**
   * Dispatches the current request.
   */
  public function dispatch()
  {
    $this->getController()->dispatch();
    
    $this->dispatcher->notify(new sfEvent($this, 'dm.context.end'));
  }

  /*
   * Means that request has been sent by a human, and the application will send html for a browser.
   * CLI, ajax and flash are NOT human.
   * @return boolean $human
   */
  public function isHtmlForHuman()
  {
    if (null !== $this->isHtmlForHuman)
    {
      return $this->isHtmlForHuman;
    }

    return $this->isHtmlForHuman =
        !dmConfig::isCli()
    &&  !$this->getRequest()->isXmlHttpRequest()
    &&  !$this->getRequest()->isFlashRequest()
    &&  $this->getResponse()->isHtml();
  }

  public function isModuleAction($module, $action)
  {
    return $this->getModuleName() === $module && $this->getActionName() === $action;
  }

  /*
   * @return DmPage the current page object
   */
  public function getPage()
  {
    return $this->page;
  }

  public function setPage(DmPage $page)
  {
    $this->page = $page;
    
    $this->dispatcher->notify(new sfEvent($this, 'dm.context.change_page', array('page' => $page)));
  }
  
}