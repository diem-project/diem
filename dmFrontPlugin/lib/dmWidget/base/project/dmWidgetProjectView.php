<?php

abstract class dmWidgetProjectView extends dmWidgetBaseView
{
  protected
  $dmModule,
  $dmComponent;

  protected function configure()
  {
    parent::configure();
  
    if (!$this->dmModule = $this->getService('module_manager')->getModule($this->widget['module']))
    {
      throw new dmException('the module "%s" does not exist', $this->dmWidget->get('module'));
    }

    if (!$this->dmComponent = $this->dmModule->getComponent($this->widget['action']))
    {
      throw new dmException(sprintf('the action "%s" does not exist for module "%s"', $this->widget['action'], $this->dmModule));
    }
  }

  protected function getPartialModuleAction()
  {
    return array($this->widget['module'], $this->widget['action']);
  }
}