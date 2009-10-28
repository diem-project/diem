<?php
/**
 */
class PluginDmProfileTable extends myDoctrineTable
{
  public function getSeoColumns()
  {
    return array_merge(array('username', 'email'), parent::getSeoColumns());
  }
}