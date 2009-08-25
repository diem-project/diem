<?php

class dmCacheManager
{
	protected static
	  $instance;

	protected
	  $dispatcher,
	  $caches;

	public function initialize()
	{
		$this->reset();
    // event listeners
	}

	public function get($cacheName)
	{
		$cacheName = dmString::modulize($cacheName);

		if (!isset($this->caches[$cacheName]))
		{
			$this->caches[$cacheName] = new dmMetaCache(array(
			  'prefix' => $cacheName
			));
		}

		return $this->caches[$cacheName];
	}

	/*
	 * remove all cache instances created
	 * does NOT clear caches content
	 */
	public function reset()
	{
    $this->caches = array();
	}

  public function clearAll($throw_exceptions = false)
  {
    $success = true;

    // Always clear file cache
    $success &= self::clearFile($throw_exceptions);

    if (dmAPCCache::isEnabled())
    {
      $success &= self::clearApc();
    }

    return $success;
  }

  protected static function clearApc()
  {
    dmDebug::log("CLEAR ALL APC");
    apc_clear_cache();
    return apc_clear_cache('user');
  }

  protected static function clearFile($throw_exceptions = false)
  {
    $success = dmFilesystem::get()->deleteDirContent(sfConfig::get('sf_cache_dir'), $throw_exceptions);

    return $success;
  }

	public function __construct(sfEventDispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/*
	 * Static methods
	 */

	public static function getCache($cacheName)
	{
		return self::getInstance()->get($cacheName);
	}

  public static function getInstance()
  {
    if (is_null(self::$instance))
    {
      throw new dmException('No instance of dmCacheManager were created');
    }

    return self::$instance;
  }

  public static function createInstance(sfEventDispatcher $dispatcher, $class = __CLASS__)
  {
    $manager = new $class($dispatcher);
    if (!$manager instanceof dmCacheManager)
    {
      throw new dmException('Cache manager must be an instance of dmCacheManager');
    }
    $manager->initialize();
    self::$instance = $manager;
  }
}