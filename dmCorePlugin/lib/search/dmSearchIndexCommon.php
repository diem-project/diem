<?php

abstract class dmSearchIndexCommon extends dmConfigurable
{
  protected
  $dispatcher,
  $serviceContainer;

  public function __construct(sfServiceContainer $serviceContainer, array $options = array())
  {
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize($options);
  }
  
  protected function initialize(array $options)
  {
    $this->configure($options);
  }

  /**
   * Gets the index name.
   *
   * @returns string
   */
  public function getName()
  {
    return $this->getOption('name');
  }
  
  public function setName($name)
  {
    return $this->setOption('name', $name);
  }
  
  public function getFullPath()
  {
    return dmProject::rootify($this->getOption('dir'));
  }

  public function fixPermissions()
  {
    $currentUmask = umask();
    umask(0000);
    
    @chmod($this->getFullPath(), 0777);
    
    foreach (sfFinder::type('all')->in($this->getFullPath()) as $item)
    {
      @chmod($item, 0777);
    }
    
    umask($currentUmask);
  }
}