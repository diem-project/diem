<?php

/**
 * @see sfWidgetFormInputFile
 */
class sfWidgetFormDmInputFile extends sfWidgetFormInputFile
{
  /**
   * Remove value attribute for html5 validation
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return $this->renderTag('input', array_merge(array('type' => $this->getOption('type'), 'name' => $name), $attributes));
  }
}
