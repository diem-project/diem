<?php

abstract class dmWidgetPluginView extends dmWidgetBaseView
{
	protected function doRenderPartial(array $vars)
	{
    $module = 'dmWidget';
    $action = $this->widgetType->getFullKey();
    
    if ($this->widgetType->useComponent())
    {
      $html = $this->helper->renderComponent($module, $action, $vars);
    }
    else
    {
      $html = $this->helper->renderPartial($module, $action, $vars);
    }
	}
}