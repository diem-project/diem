<?php

class dmPatternRouting extends sfPatternRouting
{
  public function initialize(sfEventDispatcher $dispatcher, sfCache $cache = null, $options = array())
  {
    $options = array_merge(array(
      'lookup_cache_dedicated_keys'      => dmAPCCache::isEnabled(),
      'generate_shortest_url'            => true
    ), $options);

    /*
     * Performance cost on debug on is too high
     */
    $options['debug'] = false;

    parent::initialize($dispatcher, $cache, $options);
  }

}