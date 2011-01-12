<?php
class dmDoctrineColumn extends sfDoctrineColumn
{
  public function isColumnAggregationKeyField()
  {
    return $this->isColumnAggregationKeyField;
  }
  
  public function markAsColumnAggregationKeyField()
  {
  	$this->isColumnAggregationKeyField = true;
  }
}