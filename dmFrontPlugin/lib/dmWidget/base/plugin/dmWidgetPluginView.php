<?php

abstract class dmWidgetPluginView extends dmWidgetBaseView
{
  protected function doRenderPartial(array $vars)
  {
    $module = 'dmWidget';
    $action = $this->widgetType->getFullKey();
    
    if ($this->widgetType->useComponent())
    {
      $html = $this->getHelper()->renderComponent($module, $action, $vars);
    }
    else
    {
      $html = $this->getHelper()->renderPartial($module, $action, $vars);
    }
    
    return $html;
  }
}