<?php

class sfWidgetFormDmDoctrineChoice extends sfWidgetFormDoctrineChoice
{

  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    
    $table = dmDb::table($options['model']);
    
    if ($table instanceof dmDoctrineTable)
    {
      $this->addOption('query', $table->getDefaultQuery());
    }
  }
}