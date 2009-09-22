<?php

/*
 * Base table for table export
 * Generate a string export in csv format
 * of the table records selected by query
 */
abstract class dmDoctrineTableExport
{
  protected
  $table,
  $fields,
  $customGetters;
  
  public function __construct(myDoctrineTable $table, array $options = array())
  {
    $this->table = $table;
    
    $this->configure($options);
  }
  
  public function getDefaultOptions()
  {
    return array(
      'query'     => $this->table->createQuery(),
      'format'    => 'csv',
      'extension' => 'csv',
      'encoding'  => 'utf-8'
    );
  }
  
  public function configure(array $options = array())
  {
    $this->options = array_merge($this->getDefaultOptions(), $options);
    
    $this->fields = $this->getFields();
    
    $this->customGetters = $this->findCustomGetters();
    
    $this->postConfigure();
  }
  
  protected function postConfigure()
  {
  }
  
  protected function getFields()
  {
    return dmArray::valueToKey($this->table->getAllColumnNames());
  }
  
  protected function findCustomGetters()
  {
    $getters = array();
    
    foreach(array_keys($this->fields) as $field)
    {
      $getter = 'get'.dmString::camelize($field);
      
      if (method_exists($this, $getter))
      {
        $getters[$field] = $getter;
      }
    }
    
    return $getters;
  }
  
  public function generate()
  {
    $header = $this->generateHeader();
    $rows   = $this->generateRows();
    
    $exportArray = array_merge(array($header), $rows);
    
    return $exportArray;
  }
  
  protected function getRecords()
  {
    return $this->options['query']->fetchRecords();
  }
  
  protected function generateHeader()
  {
    $fields = array();
    foreach($this->fields as $field => $fieldName)
    {
      $fields[] = dm::getI18n()->__(dmString::humanize($fieldName));
    }
    
    return $fields;
  }
  
  protected function generateRows()
  {
    $rows = array();
    
    foreach($this->getRecords() as $record)
    {
      $rows[] = $this->generateRow($record);
    }

    return $rows;
  }
  
  protected function generateRow(myDoctrineRecord $record)
  {
    $row = array();
    
    foreach($this->fields as $field => $fieldName)
    {
      try
      {
        $cell = $this->generateCell($field, $record);
      }
      catch(Exception $e)
      {
        $cell = dm::getI18n()->__('Error');
        
        if(sfConfig::get('sf_debug'))
        {
          throw $e;
          $cell .= ' '.$e->getMessage();
        }
      }
      
      $row[] = $cell;
    }
    
    return $row;
  }
  
  protected function generateCell($field, myDoctrineRecord $record)
  {
    if (isset($this->customGetters[$field]))
    {
      $getter = $this->customGetters[$field];
      $value = $this->$getter($record);
    }
    else
    {
      try
      {
        $value = $record->get($field);
      }
      catch(Doctrine_Record_UnknownPropertyException $e)
      {
        $value = $this->call($record, $field);
      }
    }
    
    if(empty($value))
    {
      $value = null;
    }
    elseif(is_string($value))
    {
      $value = trim($value);
    }
    elseif(is_object($value))
    {
      if(method_exists($value, '__toExport'))
      {
        $value = (string) $value->__toExport();
      }
      else
      {
        $value = (string) $value;
      }
      
      $value = trim($value);
    }
    
    return $value;
  }
  
  protected function call($record, $field)
  {
    throw new dmException(sprintf('The %s field does not exist in %s record', $field, get_class($record)));
  }
}