<?php

class dmProjectConfiguration extends sfProjectConfiguration
{

  public function setup()
  {
    parent::setup();

    $this->enablePlugins($this->getDependancePlugins());

    $this->setPluginPath('dmCorePlugin', dm::getDir().'/dmCorePlugin');
    $this->enablePlugins('dmCorePlugin');

    $this->setPluginPath('dmGuardPlugin', dm::getDir().'/dmGuardPlugin');
    $this->enablePlugins('dmGuardPlugin');
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
    Doctrine::debug(sfConfig::get('dm_debug'));

    /*
     * Set up doctrine extensions dir
     */
//    Doctrine::setExtensionsPath(sfConfig::get('dm_core_dir').'/lib/doctrine/extension');

    /*
     * Configure inheritance
     */
    $manager->setAttribute(Doctrine::ATTR_QUERY_CLASS, 'myDoctrineQuery');
    $manager->setAttribute(Doctrine::ATTR_COLLECTION_CLASS, 'myDoctrineCollection');
    
    /*
     * Configure charset
     */
    $manager->setCharset('utf8');
    $manager->setCollate('utf8_unicode_ci');
    
    /*
     * Configure hydrators
     */
    $manager->registerHydrator('dmFlat', 'Doctrine_Hydrator_dmFlat');
    
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
      $driver = new Doctrine_Cache_Apc(array('prefix' => dmProject::getKey().'/doctrine/'));
      
      $manager->setAttribute(Doctrine::ATTR_QUERY_CACHE, $driver);
      
      if(sfConfig::get('dm_cache_result_enabled'))
      {
        $manager->setAttribute(Doctrine::ATTR_RESULT_CACHE, $driver);
        $manager->setAttribute(Doctrine::ATTR_RESULT_CACHE_LIFESPAN, 24 * 60 * 60);
      }
    }
  }

}