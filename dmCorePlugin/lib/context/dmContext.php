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
    $this->loadServiceContainer();

    $this->configureUser();
    
    $this->configureResponse();

    /*
     * Doctrine cache configuration require a dmContext
     */
    $this->getDoctrineConfig()->configureCache();

    /*
     * Connect the tree watcher to make it aware of database modifications
     */
    $this->getPageTreeWatcher()->connect();
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
    /*
     * Response require asset configuration
     */
    $this->sfContext->getResponse()->setAssetConfig(include($this->sfContext->getConfigCache()->checkConfig('config/dm/assets.yml')));
    
    /*
     * Response require cdn configuration
     */
    $this->sfContext->getResponse()->setCdnConfig(array(
      'css' => sfConfig::get('dm_css_cdn', array('enabled' => false)),
      'js'  => sfConfig::get('dm_js_cdn', array('enabled' => false))
    ));
  
    /*
     * Enable stylesheet compression
     */
    if (sfConfig::get('dm_css_compress', true) && !sfConfig::get('dm_debug'))
    {
      $this->serviceContainer->getService('stylesheet_compressor')->connect();
    }
    
    /*
     * Enable javascript compression
     */
    if (sfConfig::get('dm_js_compress', true) && !sfConfig::get('dm_debug'))
    {
      $this->serviceContainer->getService('javascript_compressor')->connect();
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

    if (!is_dir(sfConfig::get('dm_cache_dir')))
    {
      $oldUmask = umask(0);
      mkdir(sfConfig::get('dm_cache_dir'), 0777);
      umask($oldUmask);
    }
    if (!is_dir(dirname($file)))
    {
      $oldUmask = umask(0);
      mkdir(dirname($file), 0777);
      umask($oldUmask);
    }
     
    if (/*!sfConfig::get('sf_debug') && */file_exists($file))
    {
      require_once($file);
      $this->serviceContainer = new $name;
    }
    else
    {
      $this->loadServiceContainerLoader();
       
      $sc = new sfServiceContainerBuilder;

      $configFiles = $this->sfContext->getConfiguration()->getConfigPaths('config/dm/services.yml');

      $loader = new sfServiceContainerLoaderFileYaml($sc);
      $loader->load($configFiles);
       
      if (!file_exists($file)/* || !sfConfig::get('sf_debug')*/)
      {
        $dumper = new sfServiceContainerDumperPhp($sc);
        file_put_contents($file, $dumper->dump(array('class' => $name)));
        chmod($file, 0777);
      }

      $this->serviceContainer = $sc;
    }

    $this->configureServiceContainer();

    $this->sfContext->getEventDispatcher()->notify(new sfEvent($this, 'dm.context.service_container_loaded', $this->serviceContainer));
  }

  protected function configureServiceContainer()
  {
    $this->serviceContainer->setService('dispatcher', $this->sfContext->getEventDispatcher());
    $this->serviceContainer->setService('user', $this->sfContext->getUser());
    $this->serviceContainer->setService('request', $this->sfContext->getRequest());
    $this->serviceContainer->setService('response', $this->sfContext->getResponse());
    $this->serviceContainer->setService('i18n', $this->sfContext->getI18n());
    $this->serviceContainer->setService('routing', $this->sfContext->getRouting());
    $this->serviceContainer->setService('action_stack', $this->sfContext->getActionStack());
    $this->serviceContainer->setService('config_cache', $this->sfContext->getConfigCache());
    $this->serviceContainer->setService('controller', $this->sfContext->getController());
    $this->serviceContainer->setService('context', $this->sfContext);
    $this->serviceContainer->setService('service_container', $this->serviceContainer);
    $this->serviceContainer->setService('doctrine_manager', Doctrine_Manager::getInstance());
    
    $this->serviceContainer->addParameters(array(
      'request.relative_url_root' => $this->sfContext->getRequest()->getRelativeUrlRoot()
    ));
  }

  public function loadServiceContainerLoader()
  {
    $loaderFilePrefix = dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/sfService/sfService');
    
    if (!class_exists('sfServiceContainerBuilder', false))
    {
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
    && strpos($this->sfContext->getResponse()->getContentType(), 'text/html') === 0
    );
  }

  /*
   * @return DmPage or null
   */
  public function getPage()
  {
    return null;
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