<?php

class dmValidatorCssColor extends sfValidatorRegex
{

  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setMessage('invalid', 'This color is not valid.');

    $this->setOption('pattern', '/^#?(\d|[a-f]){6}$/i');
  }
}