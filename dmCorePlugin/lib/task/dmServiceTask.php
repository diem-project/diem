<?php

abstract class dmServiceTask extends dmBaseTask
{

  public function initialize(sfEventDispatcher $dispatcher, sfFormatter $formatter)
  {
    parent::initialize($dispatcher, $formatter);

    /*
     * Redirects the service logs to the command logs
     */
    $this->dispatcher->connect('dm.service.log', array($this, 'dispatchLogToCommandLog'));
    /*
     * Redirects the service alerts to the command log
     */
    $this->dispatcher->connect('dm.service.alert', array($this, 'dispatchAlertToCommandLog'));
  }

  public function dispatchLogToCommandLog(sfEvent $event)
  {
  	$this->log(dmArray::get($event->getParameters(), 'message'));
    return true;
  }

  public function dispatchAlertToCommandLog(sfEvent $event)
  {
    $this->logSection($this->name, "### ALERT ### ".dmArray::get($event->getParameters(), 'message'));
    return true;
  }

  protected function executeService($name, $options = array())
  {
    $serviceClass = $name."Service";

    if (!class_exists($serviceClass))
    {
    	throw new dmException($serviceClass." does not exists");
    }

  	$service = new $serviceClass($this->dispatcher, $this->formatter);
    $service->addOptions($options);
    return $service->execute();
  }

}