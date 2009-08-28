<?php
/**
 * A logger that interacts with the task system to present the results.
 */
class dmLoggerTask implements dmLogger
{
  /**
   * The event dispatcher
   *
   * @var sfEventDispatcher
   */
  protected $dispatcher;

  /**
   * The formatter
   *
   * @var sfFormatter
   */
  protected $formatter;

  /**
   * Constructor to set dispatcher and formatter.
   *
   * @param sfEventDispatcher $dispatcher
   * @param sfFormatter $formatter
   */
  public function __construct(sfEventDispatcher $dispatcher, sfFormatter $formatter)
  {
    $this->dispatcher = $dispatcher;
    $this->formatter = $formatter;
  }

  /**
   * @see dmLogger
   */
  public function log($message, $section = 'sfSearch')
  {
    $message = preg_replace('/"(.+?)"/e', '$this->formatter->format("\\1", array("fg" => "blue", "bold" => true));', $message);
    $message = preg_replace('/\.{3}$/e', '$this->formatter->format("...", array("fg" => "red", "bold" => true));', $message);
    $message = preg_replace('/(Warning|Error)!/e', '$this->formatter->format("\\1!", array("fg" => "red", "bold" => true));', $message);

    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->format($section, array('fg' => 'green', 'bold' => true)) . ' >> ' . $message)));
  }
}
