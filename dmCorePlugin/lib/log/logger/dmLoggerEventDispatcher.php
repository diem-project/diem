<?php

/**
 * A logger wrapper for the event dispatcher
 */
class dmLoggerEventDispatcher implements dmLogger
{
  /**
   * The event dispatcher
   *
   * @var sfEventDispatcher
   */
  protected $dispatcher;

  /**
   * The event to notify
   *
   * @var string
   */
  protected $event = 'dm.log';

  /**
   * Constructor to set event dispatcher
   *
   * @param sfEventDispatcher $dispatcher
   */
  public function __construct(sfEventDispatcher $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }

  /**
   * Sets the event to notify
   *
   * @param string $event
   */
  public function setEventName($event)
  {
    $this->event = $event;
  }

  /**
   * @see xfLogger
   */
  public function log($message, $section = 'dm')
  {
    $this->dispatcher->notify(new sfEvent($this, $this->event, array($message, 'section' => $section)));
  }
}
