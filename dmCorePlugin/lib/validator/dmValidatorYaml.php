<?php

class dmValidatorYaml extends sfValidatorString
{
  
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setMessage('invalid', 'This is not a valid YAML definition.');
  }
  
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