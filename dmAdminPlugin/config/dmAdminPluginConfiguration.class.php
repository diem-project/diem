<?php
class dmAdminPluginConfiguration extends sfPluginConfiguration
{
  protected static
    $dependencies = array(),
    $helpers = array('DmAdmin'),
    $externalModules = array('sfGuardUser', 'sfGuardPermission', 'sfGuardGroup', 'sfPixlr');

  public function configure()
  {
    sfConfig::set('dm_admin_dir', realpath(dirname(__FILE__)."/.."));
    
    require_once(sfConfig::get('dm_admin_dir').'/lib/config/dmAdminRoutingConfigHandler.php');
  }

  public function initialize()
  {
    $this->loadConfiguration();

    $this->enableModules();

    $this->enableHelpers();

    $this->connectEvents();
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
    $modules = array_merge(self::$externalModules, $modules);

  	return $modules;
  }

  protected function enableHelpers()
  {
  	sfConfig::set('sf_standard_helpers', array_unique(array_merge(self::$helpers, sfConfig::get('sf_standard_helpers', array()))));
  }

  protected function loadConfiguration()
  {
    sfConfig::add(array(
      'sf_csrf_secret' => false,   // csrf is useless because all admin app is secured
      'sf_login_module' => 'dmAuth',
	    'sf_login_action' => 'signin',
	    'sf_secure_module' => 'dmAuth',
	    'sf_secure_action' => 'secure'
    ));
  }

  protected function connectEvents()
  {
    $this->dispatcher->connect('context.load_factories', array($this, 'loadContext'));
  }

  public function loadContext(sfEvent $event)
  {
    dmAdminContext::createInstance($event->getSubject());
  }
}