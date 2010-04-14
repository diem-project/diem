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
  
  public function setBrowserName($name)
  {
    $this->browserName = $name;
  }

  public function setBrowserVersion($version)
  {
    $this->browserVersion = $version;
  }

  public function setOperatingSystem($operatingSystem)
  {
    $this->operatingSystem = $operatingSystem;
  }

  public function __toString()
  {
    return $this->getFullName();
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

  /**
   * @return string combined browser name and version
   */
  public function getFullName()
  {
    return $this->getName().' '.$this->getVersion();
  }
}