<?php

class dmBrowser
{
  protected
  $dispatcher,
  $browserDetection,
  $name,
  $version;
  
  public function __construct(sfEventDispatcher $dispatcher, dmBrowserDetection $browserDetection)
  {
    $this->dispatcher = $dispatcher;
    $this->browserDetection = $browserDetection;

    $this->initialize();
  }

  protected function initialize()
  {
    $this->name = $this->version = null;
  }
  
  /*
   * Minimal browser detection from user agent.
   * It has the advantage of being compact and
   * fairly performant as well, since it doesn't
   * do any iteration or recursion.
   */
  public function configureFromUserAgent($userAgent)
  {
    $this->initialize();

    $infos = $this->browserDetection->execute($userAgent);
    $this->setName($infos['name']);
    $this->setVersion($infos['version']);

    if($this->isUnknown())
    {
      $this->dispatcher->notify(new sfEvent($this, 'dm.browser.unknown', $userAgent));
    }
  }
  
  public function isUnknown()
  {
    return null === $this->name;
  }
  
  public function getName()
  {
    return $this->name;
  }
  
  public function setName($name)
  {
    $this->name = $name;
  }
  
  public function getVersion()
  {
    return $this->version;
  }
  
  public function setVersion($version)
  {
    $this->version = $version;
  }
  
  public function __toString()
  {
    return $this->getFullName();
  }
  
  public function describe()
  {
    return array(
      'name'        => $this->getName(),
      'version'     => $this->getVersion(),
      'is_unknown'  => $this->isUnknown()
    );
  }
  
  public function getFullName()
  {
    return $this->getName().' '.$this->getVersion();
  }
}