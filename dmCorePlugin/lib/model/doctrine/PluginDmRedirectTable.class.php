<?php
/**
 */
class PluginDmRedirectTable extends myDoctrineTable
{

  public function getIdentifierColumnName()
  {
    return 'source';
  }
  
}