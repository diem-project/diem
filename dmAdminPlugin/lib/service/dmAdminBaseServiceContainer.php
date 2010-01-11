<?php

abstract class dmAdminBaseServiceContainer extends dmBaseServiceContainer
{
  protected function loadDependencies(array $dependencies)
  {
    parent::loadDependencies($dependencies);
    
    $this->setService('routing', $dependencies['context']->getRouting());
  }
  
  protected function connectServices()
  {
    parent::connectServices();

    $this->getService('bread_crumb')->connect();
  }
}