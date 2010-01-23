<?php

class dmWidgetNavigationMenuView extends dmWidgetPluginView
{
  protected
  $isIndexable = false;

  public function configure()
  {
    parent::configure();
    
    $this->addRequiredVar('elements');
  }
  
  protected function doRender()
  {
    $vars = $this->getViewVars();
    
    return '[menu]';
  }

}