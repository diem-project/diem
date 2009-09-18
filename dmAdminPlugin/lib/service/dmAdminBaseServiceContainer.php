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
  
}