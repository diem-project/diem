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

    include($this->getConfigCache()->checkConfig('config/dm/project.yml'));

    /*
     * Replace sf default culture by first culture in dm cultures configuration
     */
    sfConfig::set('sf_default_culture', dmArray::first(sfConfig::get('dm_i18n_cultures')));

    /*
     * Symfony1.3 registers sfAutoloadAgain on dev env. This causes huge performance issues.
     */
    if ($this->isDebug())
    {
    	sfAutoloadAgain::getInstance()->unregister();
    }
  }

  public function setup()
  {
  	parent::setup();
  	
    $this->enablePlugins($this->getDependancePlugins());
    
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