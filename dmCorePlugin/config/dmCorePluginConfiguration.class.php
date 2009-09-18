<?php

class dmCorePluginConfiguration extends sfPluginConfiguration
{
  protected static
    $modules = array('dmCore', 'dmAuth', 'dmService', 'dmUtil'),
    $helpers = array('Partial', 'I18N', 'Dm');

  public function configure()
  {
    sfConfig::set('dm_core_dir', realpath(dirname(__FILE__)."/.."));
    require_once(sfConfig::get('dm_core_dir').'/lib/config/dmInlineAssetConfigHandler.php');
  }

  public function initialize()
  {
    $this->loadConfiguration();

    $this->enableModules();

    $this->enableHelpers();

    $this->connectEvents();
    
    $this->fixIncludePath();
  }
  
  protected function fixIncludePath()
  {
    set_include_path(get_include_path().PATH_SEPARATOR.realpath(sfConfig::get('dm_core_dir').'/lib/vendor'));
  }


  protected function loadConfiguration()
  {
    sfConfig::add(array(
      'sf_i18n' => true,
      'sf_charset' => 'utf-8',
      'sf_upload_dir_name' => str_replace(sfConfig::get('sf_web_dir').'/', '', sfConfig::get('sf_upload_dir')),
      'app_sf_guard_plugin_remember_key_expiration_age' => 2592000, // 30 days
      'app_sf_guard_plugin_remember_cookie_name' => 'diem_remember_'.dmProject::getKey(),
      'dm_data_dir' => dmOs::join(sfConfig::get('sf_data_dir'), 'dm'),
      'dm_cache_dir' => dmOs::join(sfConfig::get('sf_cache_dir'), 'dm')
    ));
    
    if(null === sfConfig::get('lazy_cache_key'))
    {
      sfConfig::set('lazy_cache_key', true);
    }
    
    myConfig::initialize($this->dispatcher);
  }

  protected function enableModules()
  {
    $modules = array_unique(array_merge(self::$modules, sfConfig::get('sf_enabled_modules', array())));
    
    if($defaultKey = array_search('default', $modules))
    {
      unset($modules[$defaultKey]);
    }
    
    sfConfig::set('sf_enabled_modules', $modules);
  }

  protected function enableHelpers()
  {
    sfConfig::set('sf_standard_helpers', array_unique(array_merge(self::$helpers, sfConfig::get('sf_standard_helpers', array()))));
  }

  protected function connectEvents()
  {
    $eventConnector = new dmEventConnector($this->dispatcher);
    $eventConnector->connectEvents();
  }
  
  /**
   * Filters sfAutoload configuration values.
   * 
   * @param sfEvent $event  
   * @param array   $config 
   * 
   * @return array
   */
  public function filterAutoloadConfig(sfEvent $event, array $config)
  {
    $config = parent::filterAutoloadConfig($event, $config);
    
    /*
     * Do not load lib/vendor
     */
    $config['autoload'][$this->name.'_lib']['exclude'] = array('vendor');
    
    return $config;
  }
}