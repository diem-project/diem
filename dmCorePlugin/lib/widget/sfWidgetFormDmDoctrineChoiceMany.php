<?php

class sfWidgetFormDmDoctrineChoiceMany extends sfWidgetFormDmDoctrineChoice
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->setOption('multiple', true);
  }
}