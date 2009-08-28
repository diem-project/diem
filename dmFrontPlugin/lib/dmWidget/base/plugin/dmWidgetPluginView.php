<?php

abstract class dmWidgetPluginView extends dmWidgetBaseView
{
	protected function doRenderPartial(array $vars)
	{
    $module = 'dmWidget';
    $action = $this->widgetType->getFullKey();
    
    if ($this->widgetType->useComponent())
    {
      $html = dmContext::getInstance()->getHelper()->renderComponent($module, $action, $vars);
    }
    else
    {
      $html = dmContext::getInstance()->getHelper()->renderPartial($module, $action, $vars);
    }
	}
}