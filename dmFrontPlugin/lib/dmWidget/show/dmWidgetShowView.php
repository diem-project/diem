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
  
  protected function doRenderForIndex(array $vars)
  {
    $query = $this->dmModule->getTable()->createQuery('r');

    if ($vars['recordId'])
    {
      $query->addWhere('r.id = ?', $vars['recordId'])->fetchRecord();
    }
    elseif (($page = dmContext::getInstance()->getPage()) && $page->getDmModule()->hasModel())
    {
      $query->whereDescendantId($page->getDmModule()->getModel(), $page->get('record_id'), $this->dmModule->getModel());
    }
    
    $record = $query->withI18n(null, $this->dmModule->getModel())->fetchOne();
    
    return $record->toIndexableString();
  }
  
}