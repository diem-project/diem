<?php

class dmException extends sfException
{

  public function __construct($message = 'diem exception')
  {
    return parent::__construct(strip_tags($message));
  }

  /**
   * Builds an exception
   *
   * @param $something Any PHP type
   *
   * @return dmException An dmException instance that wraps the given something
   */
  public static function build($something)
  {
    if ($something instanceof Exception)
    {
      $exception = new dmException(sprintf('Wrapped %s: %s', get_class($something), $something->getMessage()));
      $exception->setWrappedException($something);
    }
    elseif(is_array($something))
    {
      $exception = new dmException(self::formatArrayAsHtml($something));
    }
    else
    {
      $exception = new dmException($something);
    }
    return $exception;
  }

}