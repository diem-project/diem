<?php

require_once(sfConfig::get('dm_core_dir').'/lib/vendor/php-user-agent/lib/phpUserAgent.php');

class dmBrowser extends phpUserAgent
{
  public function setEventDispatcher(sfEventDispatcher $dispatcher)
  {
    if($this->isUnknown())
    {
      $dispatcher->notify(new sfEvent($this, 'dm.browser.unknown', $userAgent));
    }
  }
}