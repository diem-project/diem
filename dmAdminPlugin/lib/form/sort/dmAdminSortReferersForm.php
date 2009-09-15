<?php

class dmAdminSortReferersForm extends dmAdminSortForm
{
  protected
  $records;
  
  public function configure()
  {
    $this->customizeQuery();
    
    $this->configureRecordFields();
  }
  
  protected function customizeQuery()
  {
    $relation = $this->getRelation();
    
    if (!$relation)
    {
      throw new dmException(sprintf('Can not find relation between %s and %s', $this->getModule(), $this->getParentRecord()->getDmModule()));
    }
    
    $this->options['query']->addWhere(sprintf('%s.%s = %s', $this->options['query']->getRootAlias(), $relation->getLocal(), $this->getParentRecord()->get('id')));
  }
  
  public function getParentRecord()
  {
    return $this->options['parentRecord'];
  }
  
  protected function getRelation()
  {
    return $this->getModule()->getTable()->getRelationHolder()->getLocalByClass($this->getParentRecord()->getDmModule()->getModel());
  }
}