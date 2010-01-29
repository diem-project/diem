<?php

abstract class dmWidgetProjectForm extends dmWidgetBaseForm
{
  protected
  $dmModule,
  $dmComponent;

  public function setup()
  {
    parent::setup();

    if (!$this->dmModule = self::$serviceContainer->getService('module_manager')->getModule($this->dmWidget->get('module')))
    {
      throw new dmException('the module "%s" does not exist', $this->dmWidget->get('module'));
    }

    if(!$this->dmComponent = $this->dmModule->getComponent($this->dmWidget->get('action')))
    {
      throw new dmException(sprintf('the action "%s" does not exist for module "%s"', $this->dmWidget->get('action'), $this->dmModule));
    }
  }

  public function getDmModule()
  {
    return $this->dmModule;
  }

  public function getDmComponent()
  {
    return $this->dmComponent;
  }
  
  public function getPage()
  {
    return $this->getServiceContainer()->getParameter('context.page');
  }
}