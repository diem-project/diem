<?php

class dmWidgetShowView extends dmWidgetProjectModelView
{

  public function configure()
  {
    parent::configure();

    if (!$this->allowAutoRecordId())
    {
      $this->addRequiredVar(array('recordId'));
    }
  }

  protected function allowAutoRecordId()
  {
    return dmContext::getInstance()->getPage()->getDmModule()->knows($this->dmModule);
  }
}