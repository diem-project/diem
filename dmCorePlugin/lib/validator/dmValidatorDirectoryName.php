<?php

class dmValidatorDirectoryName extends sfValidatorString
{

  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setMessage('invalid', '"%value%" is not a valid directory name.');
  }

  /**
   * @see sfValidatorUrl
   */
  protected function doClean($value)
  {
    $value = parent::doClean($value);
    
    if ($value !== dmOs::sanitizeDirName($value))
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }
    
    return $value;
  }
}