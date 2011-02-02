<?php

class dmBaseComponents extends sfComponents
{

  /**
   * @return sfEventDispatcher the event dispatcher
   */
  public function getDispatcher()
  {
    return $this->context->getEventDispatcher();
  }

  /**
   * @return dmFrontRouting the routing
   */
  public function getRouting()
  {
    return $this->context->getRouting();
  }

  /**
   * @return dmHelper the template helper
   */
  public function getHelper()
  {
    return $this->context->getHelper();
  }

  /*
   * @return dmI18n the i18n service
   */
  public function getI18n()
  {
    return $this->context->getI18n();
  }

  /**
   * @return dmFrontServiceContainer the front service container
   */
  public function getServiceContainer()
  {
    return $this->context->getServiceContainer();
  }

  /**
   * @param   string  $serviceName the name of the requested service
   * @param   string  $class an alternative class for the service
   * @return  object  the requested service instance
   */
  public function getService($serviceName, $class = null)
  {
    return $this->getServiceContainer()->getService($serviceName, $class);
  }
}