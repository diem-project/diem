<?php

class dmValidatorCssIdAndClasses extends sfValidatorRegex
{
  
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setMessage('invalid', 'This CSS class is not valid.');

    $this->setOption('pattern', '/^(\w|\d|\-|\s|\.|\#|$])*$/i');
  }
  
  /**
   * @see sfValidatorString
   */
  protected function doClean($value)
  {
    $value = parent::doClean($value);

    // replace spaces with dots
    $value = str_replace(' ', '.', trim($value));

    // replace .. and .# with .
    $value = preg_replace('|\.([\.\#]+)|', '', $value);

    // must start with a . or a #
    if(!empty($value) && $value{0} !== '.' && $value{0} !== '#')
    {
      $value = '.'.$value;
    }
    
    return $value;
  }
}