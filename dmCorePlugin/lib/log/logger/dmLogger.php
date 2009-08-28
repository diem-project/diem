<?php

/**
 * The logger interface
 */
interface dmLogger
{
  /**
   * Logs a message
   *
   * @param string $message
   */
  public function log($message, $section = 'dm');
}