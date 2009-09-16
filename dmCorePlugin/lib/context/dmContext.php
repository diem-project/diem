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
    
    $this->loadServiceContainer();
    
    $this->configureUser();
    
    $this->configureResponse();
    
    $this->configureAssetCompressor();
    
    /*
     * dmForm require dmOoHelper to process links
     */
    dmForm::setHelper($this->getService('helper'));

    /*
     * Doctrine cache configuration require a dmContext
     */
    $this->getDoctrineConfig()->configureCache();

    /*
     * Connect the tree watcher to make it aware of database modifications
     */
    $this->getPageTreeWatcher()->connect();
    
    $timer && $timer->addTime();
  }
  
  protected function configureUser()
  {
    /*
     * User require serviceContainer to create its browser
     */
    $this->sfContext->getUser()->setServiceContainer($this->serviceContainer);
  }
  
  protected function configureResponse()
  {
    if ($this->isHtmlForHuman())
    {
      $response = $this->sfContext->getResponse();
      
      /*
       * Response require asset aliases
       */
      $response->setAssetAliases(include($this->sfContext->getConfigCache()->checkConfig('config/dm/assets.yml')));
      
      /*
       * Response require cdn configuration
       */
      $response->setCdnConfig(array(
        'css' => sfConfig::get('dm_css_cdn', array('enabled' => false)),
        'js'  => sfConfig::get('dm_js_cdn', array('enabled' => false))
      ));
      
      /*
       * Response require asset configuration
       */
      $response->setAssetConfig($this->getService('asset_config'));
    }
  }
  
  protected function configureAssetCompressor()
  {
    /*
     * Enable stylesheet compression
     */
    if (sfConfig::get('dm_css_compress', true) && !sfConfig::get('dm_debug'))
    {
      $stylesheetCompressor = $this->serviceContainer->getService('stylesheet_compressor');
      $stylesheetCompressor->setOption('protect_user_assets', $this->sfContext->getUser()->can('code_editor'));
      $stylesheetCompressor->connect();
    }

    /*
     * Enable javascript compression
     */
    if (sfConfig::get('dm_js_compress', true) && !sfConfig::get('dm_debug'))
    {
      $javascriptCompressor = $this->serviceContainer->getService('javascript_compressor');
      $javascriptCompressor->setOption('protect_user_assets', $this->sfContext->getUser()->can('code_editor'));
      $javascriptCompressor->connect();
    }
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

    $this->configureServiceContainer($this->serviceContainer);

    $this->sfContext->getEventDispatcher()->notify(new sfEvent($this, 'dm.context.service_container_loaded', $this->serviceContainer));
  }

  public function configureServiceContainer(sfServiceContainer $sc)
  {
    $context  = $this->sfContext;
    
    $sc->setService('dispatcher', $context->getEventDispatcher());
    $sc->setService('user', $context->getUser());
    $sc->setService('request', $context->getRequest());
    $sc->setService('response', $context->getResponse());
    $sc->setService('i18n', $context->getI18n());
    $sc->setService('routing', $context->getRouting());
    $sc->setService('action_stack', $context->getActionStack());
    $sc->setService('config_cache', $context->getConfigCache());
    $sc->setService('controller', $context->getController());
    $sc->setService('logger', $context->getLogger());
    $sc->setService('context', $context);
    $sc->setService('service_container', $sc);
    $sc->setService('doctrine_manager', Doctrine_Manager::getInstance());
    
    $sc->setParameter('request.relative_url_root', $context->getRequest()->getRelativeUrlRoot());
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
    file_put_contents($file, $dumper->dump(array('class' => $name, 'base_class' => sfConfig::get('dm_service_container_class', 'dmServiceContainer'))));
    chmod($file, 0777);
    
    unset($dumper, $loader, $sc);
  }

  public function loadServiceContainerLoader()
  {
    if (!class_exists('sfServiceContainerBuilder', false))
    {
      $loaderFilePrefix = dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/sfService/sfService');
      
      foreach(array('Definition', 'Reference', 'ContainerBuilder', 'ContainerLoaderInterface', 'ContainerLoader', 'ContainerLoaderFile', 'ContainerLoaderFileYaml', 'ContainerDumperInterface', 'ContainerDumper', 'ContainerDumperPhp') as $requiredClassSuffix)
      {
        require_once($loaderFilePrefix.$requiredClassSuffix.'.php');
      }
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
   * @return dmSearchIndexGroup
   */
  public function getSearchEngine()
  {
    return $this->serviceContainer->getService('search_engine');
  }

  /*
   * @return dmDoctrineConfiguration
   */
  public function getDoctrineConfig()
  {
    return $this->serviceContainer->getService('doctrine_config');
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
   * @return dmFileBackup
   */
  public function getFileBackup()
  {
    return $this->serviceContainer->getService('file_backup');
  }

  /*
   * @return dmUserLog
   */
  public function getUserLog()
  {
    return $this->serviceContainer->getService('user_log');
  }

  /*
   * @return dmPageTreeWatcher
   */
  public function getPageTreeWatcher()
  {
    return $this->serviceContainer->getService('page_tree_watcher');
  }

  /*
   * @return dmOoHelper
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

      $appUrl = dm::getRequest()->getAbsoluteUrlRoot().'/'.$script;
    }

    return $appUrl;
  }
}