<?php

abstract class dmDoctrineCollection extends Doctrine_Collection
{
	/**
	 * @param $conn
	 * @return myDoctrineCollection
	 */
	public function saveGet($conn = null)
	{
		$this->save($conn);
		return $this;
	}
	
	/**
	 * @param myDoctrineRecord $record
	 * @param mixed $key
	 * @return myDoctrineCollection
	 */
	public function add($record, $key = null)
	{
		parent::add($record, $key);
		return $this;
	}
	
  /**
   * Processes the difference of the last snapshot and the current data
   *
   * an example:
   * Snapshot with the objects 1, 2 and 4
   * Current data with objects 2, 3 and 5
   *
   * The process would remove object 4
   * 
   * Diem alteration :
   * I never want translation records to be deleted.
   * It allows not to load all language translation
   * and to save a record without deleting all other translations
   *
   * @return Doctrine_Collection
   */
  public function processDiff()
  {
    if ($translationPos = strpos($this->_table->getComponentName(), 'Translation'))
    {
      $baseRecordClass = substr($this->_table->getComponentName(), 0, $translationPos);
      
      if ($baseTable = dmDb::table($baseRecordClass))
      {
        return $this;
      }
    }
    
    return parent::processDiff();
  }

  /**
   * Return array representation of this collection
   *
   * @return array An array representation of the collection
   */
  public function toDebug()
  {
    return array(
      'class' => $this->getTable()->getComponentName(),
      'data' => $this->toArray()
    );
  }
}