<?php

class dmEventConnector
{

	protected
	$dispatcher;

	public function __construct(sfEventDispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	public function connectEvents()
	{
		// sent when sfContext is initialized
    $this->dispatcher->connect('context.load_factories', array($this, 'contextLoaded'));
    
		$this->connectLoggingEvents();
	}
	
  /*
   * sfContext is now available
   */
  public function contextLoaded(sfEvent $event)
  {
    sfConfig::set('dm_debug', $event->getSubject()->getRequest()->getParameter('dm_debug', false));
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