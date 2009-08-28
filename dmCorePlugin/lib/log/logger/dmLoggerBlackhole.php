<?php

/**
 * A blackhole logger that does not log.
 */
final class dmLoggerBlackhole implements dmLogger
{
  /**
   * @see dmLogger
   */
  public function log($message, $section = 'dm')
  {
  }
}
