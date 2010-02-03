<?php

class dmPatternRouting extends sfPatternRouting
{
  public function initialize(sfEventDispatcher $dispatcher, sfCache $cache = null, $options = array())
  {
    /*
     * This option is great only if APC is active
     */
    $options['lookup_cache_dedicated_keys'] = dmAPCCache::isEnabled();

    /*
     * Performance cost on debug on is too high
     */
    $options['debug'] = false;

    parent::initialize($dispatcher, $cache, $options);
  }

  /*
   * Disable cache when the request contain a "_" paremeter
   * This parameter is set randomly by jQuery to avoid browser cache
   * Cache it quickly leads to thousands cache entries
   */
  public function findRoute($url)
  {
    if($this->cache && (strpos($this->options['context']['request_uri'], '?_=') || strpos($this->options['context']['request_uri'], '&_=')))
    {
      // remember the cache and disable it
      $cache = $this->cache;
      $this->cache = null;

      // get the result without cache
      $result = parent::findRoute($url);

      // restore the cache
      $this->cache = $cache;

      return $result;
    }
    else
    {
      return parent::findRoute($url);
    }
  }
}