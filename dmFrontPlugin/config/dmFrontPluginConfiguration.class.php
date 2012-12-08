<?php

class dmFrontPluginConfiguration extends sfPluginConfiguration
{
  protected static
  $dependencies = array(),
  $helpers = array('Dm', 'DmFront'),
  $externalModules = array('dmUser', 'dmBehaviors');

  public function configure()
  {
    sfConfig::set('dm_front_dir', realpath(dirname(__FILE__)."/.."));
    sfConfig::set('dm_context_type', 'front');

    sfOutputEscaper::markClassesAsSafe(array(
      'dmFrontPageBaseHelper',
      'dmFrontLayoutHelper',
      'dmHelper',
      'dmHtmlTag',
      'dmFrontToolBarView',
      'dmMenu'
    ));
    
    require_once(sfConfig::get('dm_core_dir').'/lib/config/dmFactoryConfigHandler.php');
    require_once(sfConfig::get('dm_front_dir').'/lib/config/dmFrontRoutingConfigHandler.php');
  }

  public function initialize()
  {
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
    foreach(glob(dmOs::join(sfConfig::get('dm_front_dir'), 'modules/*'), GLOB_ONLYDIR) as $dir)
    {
      $modules[] = basename($dir);
    }
    if($dirs = glob(dmOs::join(sfConfig::get('sf_plugins_dir'), '*/modules/*'), GLOB_ONLYDIR))
    {
      foreach($dirs as $dir)
      {
        if ('Admin' !== substr($dir, -5))
        {
          $modules[] = basename($dir);
        }
      }
    }

    return array_unique(array_merge($modules, self::$externalModules));
  }

  protected function enableHelpers()
  {
    sfConfig::set('sf_standard_helpers', array_unique(array_merge(self::$helpers, sfConfig::get('sf_standard_helpers', array()))));
  }
}
