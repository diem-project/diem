<?php
/**
 */
class PluginDmTransUnitTable extends myDoctrineTable
{

  public function getIdentifierColumnName()
  {
    return 'source';
  }
  
}