<?php
class dmAdminPluginConfiguration extends sfPluginConfiguration
{
  protected static
  $dependencies = array(),
  $helpers = array('Dm'),
  $externalModules = array('dmAuth', 'dmUser', 'dmPermission', 'dmGroup', 'sfPixlr');

  public function configure()
  {
    sfConfig::set('dm_admin_dir', realpath(dirname(__FILE__)."/.."));
    sfConfig::set('dm_context_type', 'admin');
    
    require_once(sfConfig::get('dm_core_dir').'/lib/config/dmFactoryConfigHandler.php');
    require_once(sfConfig::get('dm_core_dir').'/lib/config/dmModuleManagerConfigHandler.php');
    require_once(sfConfig::get('dm_admin_dir').'/lib/config/dmAdminRoutingConfigHandler.php');
  }

  public function initialize()
  {
    $this->loadConfiguration();

    $this->enableModules();

    $this->enableHelpers();
  }

  protected function enableModules()
  {
    sfConfig::set('sf_enabled_modules', array_unique(array_merge($this->getAvailableModules(), sfConfig::get('sf_enabled_modules', array()))));
  }

  protected function getAvailableModules()
  {
    $modules = array();
    foreach(glob(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/*'), GLOB_ONLYDIR) as $dir)
    {
      $modules[] = basename($dir);
    }

    return array_unique(array_merge($modules, self::$externalModules));
  }

  protected function enableHelpers()
  {
    sfConfig::set('sf_standard_helpers', array_unique(array_merge(self::$helpers, sfConfig::get('sf_standard_helpers', array()))));
  }

  protected function loadConfiguration()
  {
    sfConfig::add(array(
      'sf_csrf_secret' => false,   // csrf is useless because all admin app is secured
    ));
  }

}