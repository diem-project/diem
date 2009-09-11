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
	$query,
	$fields,
	$customGetters;
	
	public function __construct(myDoctrineTable $table, myDoctrineQuery $query = null)
	{
		$this->table = $table;
		$this->query = null === $query ? $table->createQuery() : $query;
		
		$this->configure();
	}
	
	public function configure()
	{
		$this->fields = dmArray::valueToKey($this->table->getAllColumnNames());
		
		$this->customGetters = array();
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
    return $this->query->fetchRecords();
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
		
		foreach($this->fields as $field)
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
			$value = $this->$getter($field, $record);
		}
		else
		{
			$value = $record->get($field);
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
}