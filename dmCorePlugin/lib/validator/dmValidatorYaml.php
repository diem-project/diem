<?php

class dmValidatorYaml extends sfValidatorString
{
  /**
   * @see sfValidatorUrl
   */
  protected function doClean($value)
  {
    $value = parent::doClean($value);
    
    try
    {
      $array = sfYaml::load($value);
    }
    catch(InvalidArgumentException $e)
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }
    
    return $value;
  }
}