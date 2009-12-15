<?php

abstract class dmWidgetProjectModelView extends dmWidgetProjectView
{

  protected function configure()
  {
    parent::configure();
    
    if (!$this->dmModule->getTable())
    {
      throw new dmException(sprintf('the module "%s" has no table', $this->dmModule->getKey()));
    }
  }
}