<?php

class dmValidatorCssSize extends sfValidatorRegex
{

  protected function configure($options = array(), $messages = array())
  {
    $messages['out_of_range'] = 'Enter a size between 0 and 8000';

    parent::configure($options, $messages);

    $this->setMessage('invalid', 'This size is not valid.');

    $this->setOption('pattern', '/^[\d\.]+(%|px|em|ex|pt|cm)?$/i');
  }
  
  /**
   * @see sfValidatorRegex
   */
  protected function doClean($value)
  {
    $clean = parent::doClean($value);

    $value = (float) $value;
    
    if ($value < 0 || $value > 8000)
    {
      throw new sfValidatorError($this, 'out_of_range', array('value' => $value));
    }

    return $clean;
  }
}