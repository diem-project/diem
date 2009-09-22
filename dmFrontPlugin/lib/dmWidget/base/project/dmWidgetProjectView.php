<?php

abstract class dmWidgetProjectView extends dmWidgetBaseView
{
  protected
  $dmModule,
  $dmAction;

  protected function configure()
  {
    parent::configure();

    $this->dmModule = $this->moduleManager->getModule($this->widget['module']);

    $this->dmAction = $this->dmModule->getAction($this->widget['action']);
  }

  protected function doRenderPartial(array $vars)
  {
    $module = $this->widget['module'];
    $action = $this->widget['action'];

    if ($this->widgetType->useComponent())
    {
      $html = $this->helper->renderComponent($module, $action, $vars);
    }
    else
    {
      $html = $this->helper->renderPartial($module, $action, $vars);
    }

    return $html;
  }
}