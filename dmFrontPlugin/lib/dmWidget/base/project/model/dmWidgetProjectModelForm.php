<?php

abstract class dmWidgetProjectModelForm extends dmWidgetProjectForm
{

  public function setup()
  {
    parent::setup();

    if (!$this->dmModule->hasModel())
    {
      throw new dmException('the module "%s" has no model');
    }
  }
}