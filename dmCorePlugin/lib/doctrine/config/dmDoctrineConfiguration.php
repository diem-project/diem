<?php

class dmDoctrineConfiguration
{
  protected
  $manager,
  $dispatcher,
  $cacheManager;

  public function __construct(Doctrine_Manager $manager, sfEventDispatcher $dispatcher, dmCacheManager $cacheManager)
  {
    $this->manager = $manager;
    $this->dispatcher = $dispatcher;
    $this->cacheManager = $cacheManager;
  }

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
    $this->activateCache(false);
  }

  protected function getCacheDriver()
  {
    return new Doctrine_Cache_Dm(array(
      'cache_manager' => $this->cacheManager
    ));
  }
}