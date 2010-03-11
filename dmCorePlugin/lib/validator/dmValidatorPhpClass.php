<?php

class dmValidatorPhpClass extends sfValidatorString
{
  
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->addOption('implements');

    $this->addMessage('notfound', 'This PHP class does not exist.');

    $this->addMessage('notimplement', 'This PHP class does not implement %implements%.');
  }
  
  /**
   * @see sfValidatorString
   */
  protected function doClean($value)
  {
    $value = parent::doClean($value);

    if(!class_exists($value))
    {
      throw new sfValidatorError($this, 'notfound', array('value' => $value));
    }
    
    if($this->hasOption('implements') && $value != $this->getOption('implements'))
    {
      $class = new ReflectionClass($value);

      if(!$class->isSubclassOf($this->getOption('implements')))
      {
        throw new sfValidatorError($this, 'notimplement', array('value' => $value, 'implements' => $this->getOption('implements')));
      }
    }
    
    return $value;
  }
}