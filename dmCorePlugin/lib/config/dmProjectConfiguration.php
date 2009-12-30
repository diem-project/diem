<?php

class dmProjectConfiguration extends sfProjectConfiguration
{

  public function setup()
  {
    parent::setup();

    $this->setDmPluginPaths();

    $this->enablePlugins(array('sfDoctrinePlugin', 'dmCorePlugin', 'dmUserPlugin', 'sfWebBrowserPlugin', 'sfImageTransformPlugin'));
  }
  
  protected function setDmPluginPaths()
  {
    foreach(array('dmCorePlugin', 'dmAdminPlugin', 'dmFrontPlugin') as $rootPlugin)
    {
      $this->setPluginPath($rootPlugin, dm::getDir().'/'.$rootPlugin);
    }
    
    foreach(array('dmUserPlugin', 'dmAlternativeHelperPlugin', 'sfWebBrowserPlugin', 'sfImageTransformPlugin') as $embeddedPlugin)
    {
      $this->setPluginPath($embeddedPlugin, dm::getDir().'/dmCorePlugin/plugins/'.$embeddedPlugin);
    }
  }
  
  /*
   * @deprecated
   * Please use $this->setWebDir instead
   */
  public function setWebDirName($webDirName)
  {
    return $this->setWebDir(sfConfig::get('sf_root_dir').'/'.$webDirName);
  }
  
  public function configureDoctrine(Doctrine_Manager $manager)
  {
    Doctrine_Core::debug(sfConfig::get('dm_debug'));

    /*
     * Set up doctrine extensions dir
     */
    Doctrine_Core::setExtensionsPath(sfConfig::get('dm_core_dir').'/lib/doctrine/extension');

    /*
     * Configure inheritance
     */
    $manager->setAttribute(Doctrine_Core::ATTR_TABLE_CLASS, 'myDoctrineTable');
    $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CLASS, 'myDoctrineQuery');
    $manager->setAttribute(Doctrine_Core::ATTR_COLLECTION_CLASS, 'myDoctrineCollection');
    
    /*
     * Configure charset
     */
    $manager->setCharset('utf8');
    $manager->setCollate('utf8_unicode_ci');

    /*
     * Configure hydrators
     */
    $manager->registerHydrator('dmFlat', 'Doctrine_Hydrator_dmFlatDriver');
    
    /*
     * Configure builder
     */
    sfConfig::set('doctrine_model_builder_options', array(
      'generateTableClasses'  => true,
      'baseClassName'         => 'myDoctrineRecord',
      'baseTableClassName'    => 'myDoctrineTable',
      'suffix'                => '.class.php'
    ));
    
    $this->dispatcher->disconnect('debug.web.load_panels', array('sfWebDebugPanelDoctrine', 'listenToAddPanelEvent'));
    
    $this->dispatcher->connect('debug.web.load_panels', array('dmWebDebugPanelDoctrine', 'listenToAddPanelEvent'));
  }
  
  protected function configureDoctrineCache(Doctrine_Manager $manager)
  {
    if(sfConfig::get('dm_orm_cache_enabled', true) && dmAPCCache::isEnabled())
    {
      $driver = new Doctrine_Cache_Apc(array('prefix' => dmProject::getNormalizedRootDir().'/doctrine/'));
      
      $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, $driver);
      
      if(sfConfig::get('dm_cache_result_enabled'))
      {
        $manager->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, $driver);
        $manager->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE_LIFESPAN, 24 * 60 * 60);
      }
    }
  }

}