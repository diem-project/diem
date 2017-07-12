<?php

abstract class PluginDmTag extends BaseDmTag
{

  /**
   * Get the records of a model, which are taged with this tag
   * @param   string  $model
   * @return  array   All related records of this model
   */
  public function getRelatedRecords($model)
  {
    return $this->getRelatedRecordsQuery($model)->fetchRecords();
  }

  /**
   * Get the records of a model, which are taged with this tag
   * This method returns the doctrine query to fetch these records
   * @param   string  $model
   * @return  dmDoctrineQuery
   */
  public function getRelatedRecordsQuery($model)
  {
    $relModel = $model.'DmTag';
    
    return dmDb::table($model)->createQuery('r')
    ->where('EXISTS (SELECT rel.id FROM '.$relModel.' rel WHERE rel.id = r.id AND rel.dm_tag_id = ?)', $this->get('id'));
  }

}