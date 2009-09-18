<?php

/**
 * @see sfCsvWriter
 */
class dmCsvWriter extends sfCsvWriter
{
  public function convert($data)
  {
    $lines = array();
    
    foreach ($data as $row)
    {
      $lines[] = $this->write($row);
    }
    
    return implode("\n", $lines);
  }
}