<?php

class dmFrontPluginConfiguration extends sfPluginConfiguration
{
  protected static
  $dependencies = array(),
  $helpers = array('DmFront');

  public function configure()
  {
    sfConfig::set('dm_front_dir', realpath(dirname(__FILE__)."/.."));
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
    foreach(glob(dmOs::join(sfConfig::get('dm_front_dir'), 'modules/*'), GLOB_ONLYDIR) as $dir)
    {
      $modules[] = basename($dir);
    }
    return $modules;
  }

  protected function enableHelpers()
  {
    sfConfig::set('sf_standard_helpers', array_unique(array_merge(self::$helpers, sfConfig::get('sf_standard_helpers', array()))));
  }

  protected function loadConfiguration()
  {
    sfConfig::add(array(
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
    dmFrontContext::createInstance($event->getSubject());
  }
}