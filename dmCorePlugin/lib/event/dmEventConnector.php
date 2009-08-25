<?php

class dmEventConnector
{

	protected
	$dispatcher,
	$listener;

	public function __construct(sfEventDispatcher $dispatcher, dmEventListener $listener)
	{
		$this->dispatcher = $dispatcher;
		$this->listener = $listener;
	}

	public function connectEvents()
	{
		// sent when sfContext is initialized
    $this->dispatcher->connect('context.load_factories', array($this->listener, 'contextLoaded'));
    
		$this->connectLoggingEvents();
	}

	protected function connectLoggingEvents()
	{
    if(!dmConfig::isCli())
    {
      /*
       * Notifies errors
       */
      $this->dispatcher->connect('application.throw_exception', array('dmErrorNotifier', 'notify'));
      /*
       * Redirects the service logs to the user flash
       */
//      $this->dispatcher->connect('dm.service.log', array($this, 'dispatchEventToFlashInfo'));
      /*
       * Redirects the service alerts to the user flash
       */
      $this->dispatcher->connect('dm.service.alert', array($this, 'dispatchEventToFlashAlert'));
    }
	}

  public function dispatchEventToFlashInfo(sfEvent $event)
  {
    dm::getUser()->logInfo(dmArray::get($event->getParameters(), 'message'));
  }

  public function dispatchEventToFlashAlert(sfEvent $event)
  {
    dm::getUser()->logAlert(dmArray::get($event->getParameters(), 'message'));
  }

}