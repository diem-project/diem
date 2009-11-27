<?php

class dmValidatorCssClasses extends sfValidatorString
{
  /**
   * @see sfValidatorString
   */
  protected function doClean($value)
  {
    $value = parent::doClean($value);
    
    $value = trim(str_replace('.', ' ', $value));
    
    return $value;
  }
}