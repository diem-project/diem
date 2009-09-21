<?php

abstract class dmWidgetProjectForm extends dmWidgetBaseForm
{
  protected
  $dmModule,
  $dmAction;

  public function configure()
  {
    parent::configure();

    $this->dmModule = self::$serviceContainer->getService('module_manager')->getModule($this->dmWidget->module);

    $this->dmAction = $this->dmModule->getAction($this->dmWidget->action);
  }

}