<?php

class dmValidatorCssSize extends sfValidatorRegex
{

  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setMessage('invalid', 'This size is not valid.');

    $this->setOption('pattern', '/^\d+(%|px|)$/i');
  }
  
  /**
   * @see sfValidatorRegex
   */
  protected function doClean($value)
  {
    $clean = parent::doClean($value);

    $value = (int) $value;
    
    if ($value < 0 || $value > 8000)
    {
      throw new sfValidatorError($this, 'Enter a size between 0 and 8000', array('value' => $value));
    }

    return $clean;
  }
}