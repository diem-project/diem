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
    return $this->context->getPage() ? $this->context->getPage()->getDmModule()->knows($this->dmModule) : false;
  }
  
  protected function doRenderForIndex()
  {
    $query = $this->dmModule->getTable()->createQuery('r');

    if (!empty($this->compiledVars['recordId']))
    {
      $query->addWhere('r.id = ?', $this->compiledVars['recordId']);
    }
    elseif (($page = $this->context->getPage()) && $page->getDmModule()->hasModel())
    {
      $query->whereDescendantId($page->getDmModule()->getModel(), $page->get('record_id'), $this->dmModule->getModel());
    }
    else
    {
      return '';
    }
    
    $record = $query->withI18n(null, $this->dmModule->getModel(), 'r')->fetchOne();
    
    return $record ? $record->toIndexableString() : '';
  }
  
}