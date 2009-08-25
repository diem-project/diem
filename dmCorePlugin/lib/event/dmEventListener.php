<?php

class dmEventListener
{

	protected
	$dispatcher;

	public function __construct(sfEventDispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

  /*
   * sfContext is now available
   */
  public function contextLoaded(sfEvent $event)
  {
    sfConfig::set('dm_debug', dm::getRequest()->getParameter('dm_debug', false));
  }

}