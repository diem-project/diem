<?php

class dmContext extends sfContext
{

  protected
  $serviceContainer,
  $dmConfiguration,
  $page;

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
    
    if (method_exists($this->configuration, 'configureServiceContainer'))
    {
      $this->configuration->configureServiceContainer($this->serviceContainer);
    }

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

    dmDoctrineQuery::setModuleManager($this->getModuleManager());
    dmDoctrineTable::setModuleManager($this->getModuleManager());
    dmDoctrineTable::setEventDispatcher($this->dispatcher);

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


  /**
   * Loads the symfony factories.
   */
  public function loadFactories()
  {
    $this->reloadModuleManager();

    parent::loadFactories();

    $this->factories['response']->setIsHtmlForHuman(
    !dmConfig::isCli()
    &&  !$this->factories['request']->isXmlHttpRequest()
    &&  !$this->factories['request']->isFlashRequest()
    &&  $this->factories['response']->isHtml()
    );
    
    $this->factories['i18n']->setUseAdminCatalogue($this->getUser()->can('admin'));
  }
  
  public function reloadModuleManager()
  {
    // create a new module_manager
    $this->factories['module_manager'] = include($this->getConfigCache()->checkConfig('config/dm/modules.yml'));
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
    @chmod($file, 0777);

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

  /*
   * @return dmModuleManager
   */
  public function getModuleManager()
  {
    return $this->factories['module_manager'];
  }

  /**
   * Retrieves the mailer.
   *
   * @return sfMailer The current sfMailer implementation instance.
   */
  public function getMailer()
  {
    if (!isset($this->factories['mailer']))
    {
      Swift::registerAutoload();
      sfMailer::initialize();
      $this->factories['mailer'] = new $this->mailerConfiguration['class']($this->dispatcher, $this->mailerConfiguration);
    }

    return $this->factories['mailer'];
  }

  /**
   * Dispatches the current request.
   */
  public function dispatch()
  {
    $this->checkProjectIsSetup();
    
    $this->getController()->dispatch();

    $this->dispatcher->notify(new sfEvent($this, 'dm.context.end'));
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

  protected function checkProjectIsSetup()
  {
    if (file_exists(dmOs::join(sfConfig::get('dm_data_dir'), 'lock')))
    {
      die('Please setup this project with the dm:setup task : "php symfony dm:setup"');
    }
  }
}