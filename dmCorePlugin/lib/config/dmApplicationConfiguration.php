<?php

/**
 * sfConfiguration represents a configuration for a symfony application.
 *
 * @package    symfony
 * @subpackage config
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfApplicationConfiguration.class.php 13947 2008-12-11 14:15:32Z fabien $
 */
abstract class dmApplicationConfiguration extends sfApplicationConfiguration
{
  
  public function getConfigPaths($configPath)
  {
    $configs = parent::getConfigPaths($configPath);
    
    usort($configs, array($this, 'sortConfigPaths'));
    
    return $configs;
  }
  
  public function sortConfigPaths($c1, $c2)
  {
    return $this->getConfigPathPriority($c1) > $this->getConfigPathPriority($c2);
  }
  
  protected function getConfigPathPriority($configPath)
  {
    // application configuration
    if (0 === strpos($configPath, sfConfig::get('sf_apps_dir')))
    {
      return 6;
    }
    // project configuration
    elseif (0 === strpos($configPath, sfConfig::get('sf_root_dir').'/config'))
    {
      return 5;
    }
    // plugin configuration
    elseif (0 === strpos($configPath, sfConfig::get('sf_plugins_dir')))
    {
      return 4;
    }
    // dmCore embedded plugin configuration
    elseif (0 === strpos($configPath, sfConfig::get('dm_core_dir').'/plugins'))
    {
      return 3;
    }
    // dmCore configuration
    elseif (0 === strpos($configPath, sfConfig::get('dm_core_dir')))
    {
      return 1;
    }
    // symfony configuration
    elseif (0 === strpos($configPath, sfConfig::get('sf_symfony_lib_dir')))
    {
      return 0;
    }
    // dm*** configuration
    elseif (0 === strpos($configPath, dm::getDir()))
    {
      return 2;
    }
    //others ( ? )
    return 4;
  }
  
  
  /**
   * @see sfProjectConfiguration
   */
  public function initConfiguration()
  {
    parent::initConfiguration();

    include($this->getConfigCache()->checkConfig('config/dm/config.yml'));

    /*
     * Replace sf default culture by first culture in dm cultures configuration
     */
    sfConfig::set('sf_default_culture', dmArray::first(sfConfig::get('dm_i18n_cultures')));

    /*
     * Symfony 1.3 registers sfAutoloadAgain on dev env. This causes huge performance issues.
     */
    if ($this->isDebug())
    {
      sfAutoloadAgain::getInstance()->unregister();
    }
    
    /*
     * Now that we have the project config, we can configure the doctrine cache
     */
    $this->configureDoctrineCache(Doctrine_Manager::getInstance());
  }
}