<?php

class dmValidatorCssClasses extends sfValidatorRegex
{
  
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setMessage('invalid', 'This CSS class is not valid.');

    $this->setOption('pattern', '/^(\w|\d|\-|\s|\.|$])*$/i');
  }
  
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