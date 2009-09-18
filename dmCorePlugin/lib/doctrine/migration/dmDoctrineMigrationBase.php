<?php

abstract class dmDoctrineMigrationBase extends Doctrine_Migration_Base
{
  
  protected function reverseColumn($upDown, $tableName, $columnName, $type = null, $length = null, array $options = array())
  {
    return $this->column($this->reverseDirection($upDown), $tableName, $columnName, $type, $length, $options);
  }
  
  protected function reverseDirection($direction)
  {
    return $direction == 'up' ? 'down' : 'up';
  }
  
}