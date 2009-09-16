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

}