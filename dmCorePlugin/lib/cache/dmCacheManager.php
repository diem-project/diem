<?php

class dmCacheManager
{
  protected
    $options,
    $isApcEnabled,
    $caches;

  public function __construct(array $options = array())
  {
    $this->initialize($options);
  }
    
  public function initialize(array $options = array())
  {
    $this->options = array_merge($this->getDefaultOptions(), $options);
    
    $this->reset();
  }
  
  protected function getDefaultOptions()
  {
    return array(
      'meta_cache_class' => 'dmMetaCache'
    );
  }

  public function getCache($cacheName)
  {
    $cacheName = dmString::modulize($cacheName);

    if (!isset($this->caches[$cacheName]))
    {
      $this->caches[$cacheName] = new $this->options['meta_cache_class'](array(
        'prefix'       => $cacheName
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

  public function clearAll()
  {
    $success = true;

    // Always clear file cache
    ob_start();
    dmFileCache::clearAll();
    $success = !ob_get_clean();
    
    if (dmAPCCache::isEnabled())
    {
      $success &= dmAPCCache::clearAll();
    }

    return $success;
  }
}