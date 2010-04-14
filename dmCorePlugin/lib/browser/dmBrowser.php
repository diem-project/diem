<?php

require_once(sfConfig::get('dm_core_dir').'/lib/vendor/php-user-agent/lib/phpUserAgent.php');

class dmBrowser extends phpUserAgent
{
  public function setEventDispatcher(sfEventDispatcher $dispatcher)
  {
    if($this->isUnknown())
    {
      $this->dispatcher->notify(new sfEvent($this, 'dm.browser.unknown', $userAgent));
    }
  }

  /**
   * @return array some informations about the brower 
   */
  public function describe()
  {
    return array(
      'name'        => $this->getName(),
      'version'     => $this->getVersion(),
      'is_unknown'  => $this->isUnknown()
    );
  }
}