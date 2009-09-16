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
  	Doctrine::debug(sfConfig::get("sf_debug"));

    /*
     * Set up doctrine extensions dir
     */
    Doctrine::setExtensionsPath(sfConfig::get('dm_core_dir').'/lib/doctrine/extension');

    /*
     * I want Doctrine to autoload table classes
     */
    $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

    /*
     * make $record->setSomething($value) override $record->_set('something', $value);
     */
    $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);

    /*
     * Enable doctrine validators
     */
    $manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);

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
    $manager->registerHydrator('dmAssoc', 'Doctrine_Hydrator_dmAssoc');
    
    /*
     * Configure builder
     */
    sfConfig::set('doctrine_model_builder_options', array(
      'generateTableClasses'  => true,
      'baseClassName'         => 'myDoctrineRecord',
      'baseTableClassName'    => 'myDoctrineTable',
      'suffix'                => '.class.php'
    ));
  }

}