<?php

abstract class dmWidgetProjectForm extends dmWidgetBaseForm
{
  protected
  $dmModule,
  $dmAction;

  public function configure()
  {
    parent::configure();

    $this->dmModule = dmModuleManager::getModule($this->dmWidget->module);

    $this->dmAction = $this->dmModule->getAction($this->dmWidget->action);
  }

}