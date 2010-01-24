<?php

abstract class dmWidgetProjectForm extends dmWidgetBaseForm
{
  protected
  $dmModule,
  $dmAction;

  public function setup()
  {
    parent::setup();

    if (!$this->dmModule = self::$serviceContainer->getService('module_manager')->getModule($this->dmWidget->get('module')))
    {
      throw new dmException('the module "%s" does not exist', $this->dmWidget->get('module'));
    }

    if(!$this->dmAction = $this->dmModule->getAction($this->dmWidget->get('action')))
    {
      throw new dmException(sprintf('the action "%s" does not exist for module "%s"', $this->dmWidget->get('action'), $this->dmModule));
    }
  }

  public function getDmModule()
  {
    return $this->dmModule;
  }

  public function getDmAction()
  {
    return $this->dmAction;
  }
  
  public function getPage()
  {
    return self::$serviceContainer->getParameter('context.page');
  }
}