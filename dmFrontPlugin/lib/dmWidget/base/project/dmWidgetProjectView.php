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
      $html = dmContext::getInstance()->getHelper()->renderComponent($module, $action, $vars);
    }
    else
    {
      $html = dmContext::getInstance()->getHelper()->renderPartial($module, $action, $vars);
    }

    return $html;
  }
}