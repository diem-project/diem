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
    $this->setService('action_stack',     $dependencies['context']->getActionStack());
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
    
    if ($this->getService('response')->isHtmlForHuman())
    {
      $this->configureResponse();
      
      $this->configureAssetCompressor();
    }
  }
  
  protected function configureUser()
  {
    $this->getService('user')->setBrowser($this->getService('browser'));
  }
  
  protected function configureResponse()
  {
    /*
     * Response require asset aliases
     */
    $this->getService('response')->setAssetAliases(include($this->getService('config_cache')->checkConfig('config/dm/assets.yml')));
    
    /*
     * Response require cdn configuration
     */
    $this->getService('response')->setCdnConfig(array(
      'css' => sfConfig::get('dm_css_cdn',  array('enabled' => false)),
      'js'  => sfConfig::get('dm_js_cdn',   array('enabled' => false))
    ));
    
    /*
     * Response require asset configuration
     */
    $this->getService('response')->setAssetConfig($this->getService('asset_config'));
  }
  
  protected function configureAssetCompressor()
  {
    /*
     * Enable stylesheet compression
     */
    if (!sfConfig::get('dm_debug'))
    {
      $stylesheetCompressor = $this->getService('stylesheet_compressor');
      $stylesheetCompressor->setOption('protect_user_assets', $this->getService('user')->can('code_editor'));
      $stylesheetCompressor->connect();
    }

    /*
     * Enable javascript compression
     */
    if (!sfConfig::get('dm_debug'))
    {
      $javascriptCompressor = $this->getService('javascript_compressor');
      $javascriptCompressor->setOption('protect_user_assets', $this->getService('user')->can('code_editor'));
      $javascriptCompressor->connect();
    }
  }
  
  public function connect()
  {
    $this->getService('dispatcher')->connect('user.change_culture', array($this, 'listenToChangeCultureEvent'));
    
    $this->getService('dispatcher')->connect('user.change_theme', array($this, 'listenToChangeThemeEvent'));
    
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

  
  /*
   * @return dmMediaResource
   */
  public function getMediaResource($source)
  {
    $resource = $this->getService('media_resource');
    $resource->initialize($source);
    
    return $resource;
  }
  
  /*
   * @return dmMediaTag
   */
  public function getMediaTag($resource)
  {
    if (!$resource instanceof dmMediaResource)
    {
      $resource = $this->getMediaResource($resource);
    }
    
    $this->setParameter('media_tag.class', $this->getParameter('media_tag_'.$resource->getMime().'.class'));
    $this->setParameter('media_tag.source', $resource);
    
    return $this->getService('media_tag');
  }
  
  /*
   * @return dmLinkResource
   */
  public function getLinkResource($source)
  {
    $resource = $this->getService('link_resource');
    $resource->initialize($source);
    
    return $resource;
  }
  
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
    $this->parameters[strtolower($name)] = array_merge($this->parameters[strtolower($name)], $value);
  }
}