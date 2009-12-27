<?php

class dmBaseComponents extends sfComponents
{
  
  protected function getHelper()
  {
    return $this->context->getHelper();
  }
  
  protected function getServiceContainer()
  {
    return $this->context->getServiceContainer();
  }
  
  protected function getService($serviceName)
  {
    return $this->getServiceContainer()->getService($serviceName);
  }
}