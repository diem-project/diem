<?php

class dmCorePluginConfiguration extends sfPluginConfiguration
{
  protected static
    $modules = array('dmCore', 'dmAuth'),
    $helpers = array('Partial', 'I18N', 'Dm');

  public function configure()
  {
    sfConfig::set('dm_core_dir', realpath(dirname(__FILE__).'/..'));
    require_once(sfConfig::get('dm_core_dir').'/lib/config/dmInlineAssetConfigHandler.php');
  }

  public function initialize()
  {
    $this->loadConfiguration();

    $this->enableModules();

    $this->enableHelpers();
    
    $this->fixIncludePath();
  }
  
  protected function fixIncludePath()
  {
    set_include_path(get_include_path().PATH_SEPARATOR.realpath(sfConfig::get('dm_core_dir').'/lib/vendor'));
  }

  protected function loadConfiguration()
  {
    sfConfig::add(array(
      'sf_i18n'             => true,
      'sf_charset'          => 'utf-8',
      'sf_upload_dir_name'  => str_replace(dmOs::normalize(sfConfig::get('sf_web_dir').'/'), '', dmOs::normalize(sfConfig::get('sf_upload_dir'))),
      'dm_data_dir'         => dmOs::join(sfConfig::get('sf_data_dir'), 'dm'),
      'dm_cache_dir'        => dmOs::join(sfConfig::get('sf_cache_dir'), 'dm')
    ));
    
    if(extension_loaded('mbstring'))
    {
      mb_internal_encoding('UTF-8');
    }
    
    dmConfig::initialize($this->dispatcher);
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