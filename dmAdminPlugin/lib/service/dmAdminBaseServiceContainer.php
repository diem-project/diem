<?php

abstract class dmAdminBaseServiceContainer extends dmBaseServiceContainer
{
  protected function loadDependencies(array $dependencies)
  {
    parent::loadDependencies($dependencies);
    
    $this->setService('routing',          $dependencies['context']->getRouting());
  }
  
  public function connect()
  {
    parent::connect();
    
    // must be called after connections to event dispatcher
    $this->getService('user')->setTheme($this->getService('theme'));
  }
  
  /*
   * @return dmAdminLinkTag
   */
  public function getLinkTag($source)
  {
    $this->setParameter('link_tag.source', $source);
    
    return $this->getService('link_tag');
  }
}