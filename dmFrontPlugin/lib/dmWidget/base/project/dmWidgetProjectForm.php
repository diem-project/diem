<?php

abstract class dmWidgetProjectForm extends dmWidgetBaseForm
{
  protected
  $dmModule,
  $dmAction;

  public function configure()
  {
    parent::configure();

    $this->dmModule = self::$serviceContainer->getService('module_manager')->getModule($this->dmWidget->get('module'));

    $this->dmAction = $this->dmModule->getAction($this->dmWidget->action);
  }

  public function getDmModule()
  {
    return $this->dmModule;
  }

  public function getDmAction()
  {
    return $this->dmAction;
  }
}