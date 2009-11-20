<?php

abstract class dmAdminSortForm extends BaseForm
{
  protected
  $records;
  
  protected function configureRecordFields()
  {
    foreach($this->getRecords() as $index => $record)
    {
      $this->configureRecordField($index, $record);
    }
  }
  
  protected function configureRecordField($index, $record)
  {
    $fieldName = $record->get('id');
    $this->widgetSchema[$fieldName] = new sfWidgetFormInputHidden;
    $this->widgetSchema[$fieldName]->setLabel($record->__toString());
    $this->validatorSchema[$fieldName] = new sfValidatorPass;
    $this->setDefault($fieldName, $index+1);
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
    $this->options['module']->getTable()->doSort($this->getValues());
  }
}