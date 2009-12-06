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
  
  /**
   * Diem override :
   * symfony getConfigPath badly handle config/dm/*.yml files
   * 
   * Gets the configuration file paths for a given relative configuration path.
   *
   * @param string $configPath The configuration path
   *
   * @return array An array of paths
   */
  public function getConfigPaths($configPath)
  {
    $globalConfigPath = basename(dirname($configPath)).'/'.basename($configPath);

    $files = array(
      $this->getSymfonyLibDir().'/config/'.$globalConfigPath, // symfony
    );

    foreach ($this->getPluginPaths() as $path)
    {
      if (is_file($file = $path.'/'.$globalConfigPath))
      {
        $files[] = $file;                                     // plugins
      }
    }

    foreach ($this->getPluginPaths() as $path)
    {
      if (is_file($file = $path.'/'.$configPath))
      {
        $files[] = $file;                                     // plugins
      }
    }

    $files = array_merge($files, array(
      $this->getRootDir().'/'.$globalConfigPath,              // project
      $this->getRootDir().'/'.$configPath,                    // project
      sfConfig::get('sf_app_dir').'/'.$globalConfigPath,      // application
      sfConfig::get('sf_app_cache_dir').'/'.$configPath,      // generated modules
    ));

    $files[] = sfConfig::get('sf_app_dir').'/'.$configPath;   // module

    $configs = array();
    foreach (array_unique($files) as $file)
    {
      if (is_readable($file))
      {
        $configs[] = $file;
      }
    }

    return $configs;
  }
  
  /*
   * Wich dmPlugins are usefull for this application ?
   * @returns array plugin names
   */
  abstract protected function getDmPlugins();
  
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

  public function setup()
  {
    parent::setup();

    $dmPlugins = $this->getDmPlugins();
    
    if (isset($dmPlugins['dmFrontPlugin']) && isset($dmPlugins['dmAdminPlugin']))
    {
      throw new Exception('Can not include both dmFrontPlugin and dmAdminPlugin');
    }

    foreach($dmPlugins as $dmPlugin)
    {
      $this->setPluginPath($dmPlugin, dm::getDir().'/'.$dmPlugin);
    }

    $this->enablePlugins($dmPlugins);
  }
}