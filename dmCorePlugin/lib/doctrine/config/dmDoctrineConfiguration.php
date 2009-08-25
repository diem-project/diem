<?php

class dmDoctrineConfiguration
{
	protected static
	$instance;

	protected
	$manager,
	$dispatcher;

	public static function getInstance()
	{
		if(is_null(self::$instance))
		{
			throw new dmException('dmDoctrineConfiguration has no instance');
		}

    return self::$instance;
	}

	public static function createInstance(Doctrine_Manager $manager, sfEventDispatcher $dispatcher)
	{
    return self::$instance = new self($manager, $dispatcher);
	}

	public function __construct(Doctrine_Manager $manager, sfEventDispatcher $dispatcher)
	{
		$this->manager = $manager;
		$this->dispatcher = $dispatcher;
	}

	public function initialize()
	{
		Doctrine::debug(sfConfig::get("sf_debug"));

		/*
		 * I want Doctrine to autoload table classes
		 */
		$this->manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

    /*
     * make $record->setSomething($value) override $record->_set('something', $value);
     */
    $this->manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);

    /*
     * Enable doctrine validators
     */
    $this->manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);

    /*
     * Set up doctrine extensions dir
     */
    Doctrine::setExtensionsPath(sfConfig::get('sf_root_dir').'/plugins/dmCorePlugin/lib/doctrine/extension');

		$this
    ->configureInheritance()
    ->configureCharset()
    ->configureBuilder()
    ->configureHydrator();
    
    $this->dispatcher->connect('context.load_factories', array($this, 'configureCache'));
  
    return $this;
	}

	public function listenConfigLoaded()
	{
    self::getInstance()->configureCache();
	}

  protected function configureInheritance()
  {
    $this->manager->setAttribute(Doctrine::ATTR_QUERY_CLASS, 'myDoctrineQuery');
    $this->manager->setAttribute(Doctrine::ATTR_COLLECTION_CLASS, 'MyDoctrineCollection');

    return $this;
  }

  protected function configureCharset()
  {
    $this->manager->setCharset('utf8');
    $this->manager->setCollate('utf8_unicode_ci');

    return $this;
  }

  protected function configureBuilder()
  {
    sfConfig::set('doctrine_model_builder_options', array(
      'generateTableClasses'  => true,
      'baseClassName'         => 'myDoctrineRecord',
      'baseTableClassName'    => 'myDoctrineTable',
      'suffix'                => '.class.php'
    ));

    return $this;
  }

  protected function configureHydrator()
  {
    $this->manager->registerHydrator('dmFlat', 'Doctrine_Hydrator_dmFlat');
    $this->manager->registerHydrator('dmAssoc', 'Doctrine_Hydrator_dmAssoc');

    return $this;
  }

  /*
   * Needs diem project config to be loaded
   */
  public function configureCache()
  {
  	if (!sfConfig::get('dm_orm_cache_enabled', true))
  	{
  		return;
  	}

    $driver = $this->getCacheDriver();

    $this->manager->setAttribute(Doctrine::ATTR_QUERY_CACHE, $driver);

//    if (sfConfig::get('dm_orm_cache_result_enabled', false))
//    {
	    $this->manager->setAttribute(Doctrine::ATTR_RESULT_CACHE, $driver);
	    $this->manager->setAttribute(Doctrine::ATTR_RESULT_CACHE_LIFESPAN, 60 * 60);
	    $this->activateCache();
//    }
//    else
//    {
//      $this->manager->setAttribute(Doctrine::ATTR_RESULT_CACHE, null);
//    }
    return $this;
  }

  public function activateCache($val = true)
  {
    sfConfig::set('dm_orm_cache_result_activated', (boolean) $val);
  }

  public function desactivateCache()
  {
    self::activateCache(false);
  }

  protected function getCacheDriver()
  {
  	return new Doctrine_Cache_Dm();
  }
}