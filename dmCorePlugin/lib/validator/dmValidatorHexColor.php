<?php

class dmValidatorHexColor extends sfValidatorRegex
{

  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setMessage('invalid', 'This is not a valid hexadecimal color');

    $this->setOption('pattern', '|^#?[\dA-F]{6}$|i');
  }
}