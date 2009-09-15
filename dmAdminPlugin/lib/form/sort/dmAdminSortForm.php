<?php

abstract class dmAdminSortForm extends BaseForm
{
  protected
  $records;
  
  protected function configureRecordFields()
  {
    foreach($this->getRecords() as $record)
    {
      $fieldName = $record->get('id');
      $this->widgetSchema[$fieldName] = new sfWidgetFormInputHidden;
      $this->widgetSchema[$fieldName]->setLabel($record->__toString());
      $this->validatorSchema[$fieldName] = new sfValidatorPass;
      $this->setDefault($fieldName, 1);
    }
  }
  
  public function getModule()
  {
    return $this->options['module'];
  }
  
  public function getRecords()
  {
    if ($this->records)
    {
      return $this->records;
    }
    
    return $this->records = $this->options['query']->fetchRecords();
  }
  
  public function save()
  {
    $values = $this->getValues();
    
    $currentPosition = 0;
    $recordPositions = array();
    
    foreach(array_keys($this->getValues()) as $recordId)
    {
      $recordPositions[$recordId] = ++$currentPosition;
    }
    
    $this->options['module']->getTable()->doSort($recordPositions);
  }
}