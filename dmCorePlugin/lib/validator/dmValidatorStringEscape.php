<?php

class dmValidatorStringEscape extends sfValidatorString
{
  /**
   * @see sfValidatorString
   */
  protected function doClean($value)
  {
    return parent::doClean(dmString::escape((string) $value));
  }
  
}