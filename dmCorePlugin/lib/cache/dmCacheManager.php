<?php

class dmCacheManager extends dmConfigurable
{
  protected
    $dispatcher,
    $caches;

  public function __construct(sfEventDispatcher $dispatcher, array $options = array())
  {
    $this->dispatcher = $dispatcher;
    
    $this->initialize($options);
  }
    
  public function initialize(array $options)
  {
    $this->configure($options);
    
    $this->reset();
  }
  
  public function getDefaultOptions()
  {
    return array(
      'meta_cache_class' => 'dmMetaCache'
    );
  }

  public function getCache($cacheName)
  {
    if (!isset($this->caches[$cacheName]))
    {
      $this->caches[$cacheName] = new $this->options['meta_cache_class'](array(
        'prefix'       => $cacheName
      ));
    }

    return $this->caches[$cacheName];
  }
  
  /**
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

    dmFileCache::clearAll();

    if(count($survivors = glob(sfConfig::get('sf_cache_dir').'/*')))
    {
      $success = false;
      $message = 'Can not be removed from cache : '.implode(', ', $survivors);
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array($message, 'priority' => sfLogger::ERR)));
    }
    
    if (dmAPCCache::isEnabled())
    {
      if(!dmAPCCache::clearAll())
      {
        $success = false;
        $message = 'Can not clear APC cache';
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array($message, 'priority' => sfLogger::ERR)));
      }
    }

    $this->dispatcher->notify(new sfEvent($this, 'dm.cache.clear', array('success' => $success)));
    $this->dispatcher->notify(new sfEvent($this, 'task.cache.clear'));

    return $success;
  }
  
}