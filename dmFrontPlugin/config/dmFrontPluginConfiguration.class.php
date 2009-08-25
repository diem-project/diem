<?php

class dmFrontPluginConfiguration extends sfPluginConfiguration
{
  protected static
    $dependencies = array(),
    $helpers = array('DmFront'),
    $external_modules = array('dmWidget');

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
      $dirs = sfFinder::type('dir')
      ->maxdepth(0)
      ->in(
        sfConfig::get('dm_front_dir').DIRECTORY_SEPARATOR.'modules',
        sfConfig::get('dm_widget_dir').DIRECTORY_SEPARATOR.'modules'
      );
      foreach($dirs as $dir)
      {
        $modules[] = basename($dir);
      }
      $modules = array_merge(self::$external_modules, $modules);

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
    $this->dispatcher->connect('routing.load_configuration', array('dmFrontRouting', 'listenToRoutingLoadConfigurationEvent'));
    $this->dispatcher->connect('context.load_factories', array($this, 'loadContext'));
  }

  public function loadContext()
  {
    $t = dmDebug::timer('retrieve site');
    $site = sfContext::getInstance()->getConfiguration()->getCurrentSite();
    $t->addTime();
    dmFrontContext::createInstance(sfContext::getInstance())->setSite($site);
  }
}