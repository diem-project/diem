<?php

class dmBaseComponents extends sfComponents
{

  protected function getHelper()
  {
    return $this->context->getHelper();
  }

  protected function getI18n()
  {
    return $this->context->getI18n();
  }
  
  protected function getServiceContainer()
  {
    return $this->context->getServiceContainer();
  }
  
  protected function getService($serviceName, $class = null)
  {
    return $this->getServiceContainer()->getService($serviceName, $class);
  }
}