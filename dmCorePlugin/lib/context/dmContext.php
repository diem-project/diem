<?php

abstract class dmContext extends dmMicroCache
{
  protected static
  $_null = null,
  $instance = null;

  protected
  $serviceContainer,
  $dmConfiguration,
  $sfContext;

  abstract public function getModule();

  public function getModuleKey()
  {
    return ($module = $this->getModule()) ? $module->getKey() : null;
  }

  public function __construct(sfContext $sfContext)
  {
    $this->sfContext = $sfContext;

    $this->initialize();
  }

  public function initialize()
  {
    $timer = dmDebug::timerOrNull('dmContext::initialize');
    
    // load the service container instance
    $this->loadServiceContainer();

    // configure the service container with its required dependencies
    $this->configureServiceContainer($this->serviceContainer);
    
    // connect the service container and its services to the event dispatcher
    $this->serviceContainer->connect();

    // notify that the service container is ready
    $this->sfContext->getEventDispatcher()->notify(new sfEvent($this, 'dm.context.service_container_loaded', array('service_container', $this->serviceContainer)));
    
    /*
     * dmHtmlTag requires service container to create link and media tags
     */
    dmHtmlTag::setDmContext($this);
    
    /*
     * dmForm requires service container...
     */
    dmForm::setServiceContainer($this->serviceContainer);
    
    /*
     * dmDoctrineRecord needs the event dispatcher to communicate !
     */
    dmDoctrineRecord::setEventDispatcher($this->sfContext->getEventDispatcher());

    /*
     * Doctrine cache configuration require a loaded dmContext to run
     */
    $this->getService('doctrine_config')->configureCache();
    
    $timer && $timer->addTime();
  }

  /*
   * @return mixed the requested service
   */
  public function getService($serviceName)
  {
    return $this->serviceContainer->getService($serviceName);
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
      'context'           => $this->sfContext,
      'doctrine_manager'  => Doctrine_Manager::getInstance()
    ),
    array(
      'human_response'    => $this->isHtmlForHuman()
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

    $this->loadServiceContainerLoader();

    $sc = new sfServiceContainerBuilder;

    $configFiles = $this->sfContext->getConfiguration()->getConfigPaths('config/dm/services.yml');

    $loader = new sfServiceContainerLoaderFileYaml($sc);
    $loader->load($configFiles);

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
  public function loadServiceContainerLoader()
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
   * @return sfLogger
   */
  public function getLogger()
  {
    return $this->serviceContainer->getService('logger');
  }

  /*
   * @return dmHelper
   */
  public function getHelper()
  {
    return $this->serviceContainer->getService('helper');
  }

  /*
   * @return sfContext
   */
  public function getSfContext()
  {
    return $this->sfContext;
  }

  public function isHtmlForHuman()
  {
    if ($this->hasCache('is_html_for_human'))
    {
      return $this->getCache('is_html_for_human');
    }

    return $this->setCache('is_html_for_human',
    !$this->sfContext->getRequest()->isXmlHttpRequest()
    && !$this->sfContext->getRequest()->isFlashRequest()
    && $this->sfContext->getResponse()->isHtml()
    );
  }

  /*
   * @return dmContext
   */
  public static function getInstance()
  {
    if (self::$_null === self::$instance)
    {
      throw new sfException('dmContext instance does not exist.');
    }

    return self::$instance;
  }

  public function isModuleAction($module, $action)
  {
    return $this->sfContext->getModuleName() === $module && $this->sfContext->getActionName() === $action;
  }

  public function getAppUrl($app = null, $env = null, $culture = null)
  {
    $app = null === $app ? sfConfig::get('sf_app') : $app;
    $env = null === $env ? sfConfig::get('sf_environment') : $env;
    $culture = null === $culture ? $this->sfContext->getUser()->getCulture() : $culture;

    $knownAppUrls = json_decode(dmConfig::get('base_urls', '[]'), true);

    $appUrlKey = implode('-', array($app, $env, $culture));

    if (!($appUrl = dmArray::get($knownAppUrls, $appUrlKey)))
    {
      if(file_exists(dmOs::join(sfConfig::get('sf_web_dir'), $app.'_'.sfConfig::get('sf_environment').'.php')))
      {
        $script = $app.'_'.sfConfig::get('sf_environment').'.php';
      }
      elseif(file_exists(dmOs::join(sfConfig::get('sf_web_dir'), $app.'.php')))
      {
        $script = $app.'.php';
      }
      elseif($app == 'front')
      {
        $script = sfConfig::get('sf_environment') == 'prod' ? 'index.php' : sfConfig::get('sf_environment').'.php';
      }
      else
      {
        throw new dmException(sprintf('Diem can not guess %s app url', $app));
      }

      $appUrl = $this->sfContext->getRequest()->getAbsoluteUrlRoot().'/'.$script;
    }

    return $appUrl;
  }
}