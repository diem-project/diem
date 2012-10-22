<?php

/**
 * Base table for table export
 * Generate a string export in csv format
 * of the table records selected by query
 */
abstract class dmDoctrineTableExport extends dmConfigurable
{
  protected
  $table,
  $i18n,
  $customGetters;

  public function __construct(myDoctrineTable $table, dmI18n $i18n, array $options = array())
  {
    $this->table  = $table;
    $this->i18n   = $i18n;

    $this->initialize($options);
  }

  public function getDefaultOptions()
  {
    return array(
      'format'    => 'csv',
      'extension' => 'csv',
      'encoding'  => 'utf-8'
    );
  }

  public function initialize(array $options)
  {
    $this->configure($options);

    $this->customGetters = $this->findCustomGetters();
  }

  /**
   * @return dmDoctrineQuery
   */
  public function getQuery($rootAlias = 'r')
  {
    return $this->table->createQuery($rootAlias);
  }

  protected function getFields()
  {
    return dmArray::valueToKey($this->table->getAllColumnNames());
  }

  protected function findCustomGetters()
  {
    $getters = array();

    foreach(array_keys($this->getFields()) as $field)
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
    return $this->getQuery()->fetchRecords();
  }

  protected function generateHeader()
  {
    $fields = array();
    foreach($this->getFields() as $field => $fieldName)
    {
      $fields[] = $this->i18n->__(dmString::humanize($fieldName));
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

    foreach($this->getFields() as $field => $fieldName)
    {
      try
      {
        $cell = $this->generateCell($field, $record);
      }
      catch(Exception $e)
      {
        $cell = $this->i18n->__('Error');

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
      $value = addslashes(trim($value));
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