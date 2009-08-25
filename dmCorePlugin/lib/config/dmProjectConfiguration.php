<?php

class dmProjectConfiguration extends sfProjectConfiguration
{

  public function setup()
  {
    parent::setup();
    
    $this->enablePlugins($this->getDependancePlugins());
    
    $this->setPluginPath('dmCorePlugin', dm::getDir().'/dmCorePlugin');
    
    $this->enablePlugins('dmCorePlugin');
  }
  
  protected function getDependancePlugins()
  {
    return array('sfDoctrinePlugin');
  }

	public function setWebDirName($webDirName)
	{
		return $this->setWebDir(sfConfig::get('sf_root_dir').'/'.$webDirName);
	}

  public function configureDoctrine(Doctrine_Manager $manager)
  {
  	include_once(dm::getDir().'/dmCorePlugin/lib/doctrine/config/dmDoctrineConfiguration.php');
  	
  	dmDoctrineConfiguration::createInstance($manager, $this->dispatcher)->initialize();
  }

}