<?php

abstract class dmWidgetPluginView extends dmWidgetBaseView
{

  public function render($vars = array())
  {
    if (!$this->isValid())
    {
      return $this->renderDefault();
    }

    $module = $this->widget['module'];
    $action = $this->widget['action'];
    $vars   = $this->getViewVars($vars);

    if ($this->widgetType->useComponent())
    {
      $html = dmContext::getInstance()->getHelper()->renderComponent('dmWidget', $this->widgetType->getFullKey(), $vars);
    }
    else
    {
      $html = dmContext::getInstance()->getHelper()->renderPartial('dmWidget', $this->widgetType->getFullKey(), $vars);
    }

    return $html;
  }
}