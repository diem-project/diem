<?php

class dmValidatorCssSize extends sfValidatorRegex
{

  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setMessage('invalid', 'This size is not valid.');

    $this->setOption('pattern', '/^\d+(%|px|)$/i');
  }
}