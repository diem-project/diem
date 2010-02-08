<?php

class dmBaseComponents extends sfComponents
{

  public function getHelper()
  {
    return $this->context->getHelper();
  }

  public function getI18n()
  {
    return $this->context->getI18n();
  }
  
  public function getServiceContainer()
  {
    return $this->context->getServiceContainer();
  }
  
  public function getService($serviceName, $class = null)
  {
    return $this->getServiceContainer()->getService($serviceName, $class);
  }
}