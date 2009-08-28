<?php

abstract class dmWidgetProjectView extends dmWidgetBaseView
{
  protected
  $dmModule,
  $dmAction;

  protected function configure()
  {
    parent::configure();

    $this->dmModule = dmModuleManager::getModule($this->widget['module']);

    $this->dmAction = $this->dmModule->getAction($this->widget['action']);
  }

  protected function doRenderPartial(array $vars)
  {
    $module = $this->widget['module'];
    $action = $this->widget['action'];

    if ($this->widgetType->useComponent())
    {
      $html = dmContext::getInstance()->getHelper()->renderComponent($module, $action, $vars);
    }
    else
    {
      $html = dmContext::getInstance()->getHelper()->renderPartial($module, $action, $vars);
    }

    return $html;
  }
}