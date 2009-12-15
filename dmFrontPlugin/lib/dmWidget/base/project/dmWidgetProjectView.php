<?php

abstract class dmWidgetProjectView extends dmWidgetBaseView
{
  protected
  $dmModule,
  $dmAction;

  protected function configure()
  {
    parent::configure();
  
    if (!$this->dmModule = $this->context->get('module_manager')->getModule($this->widget['module']))
    {
      throw new dmException('the module "%s" does not exist', $this->dmWidget->get('module'));
    }

    if (!$this->dmAction = $this->dmModule->getAction($this->widget['action']))
    {
      throw new dmException(sprintf('the action "%s" does not exist for module "%s"', $this->widget['action'], $this->dmModule));
    }
  }

  protected function doRenderPartial(array $vars)
  {
    $module = $this->widget['module'];
    $action = $this->widget['action'];

    if ($this->widgetType->useComponent())
    {
      $html = $this->context->get('helper')->renderComponent($module, $action, $vars);
    }
    else
    {
      $html = $this->context->get('helper')->renderPartial($module, $action, $vars);
    }

    return $html;
  }
}