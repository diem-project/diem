<?php

abstract class dmGaChart extends dmChart
{
  protected function reportToData($report, array $keys)
  {
    $data = array();
    
    foreach($keys as $key)
    {
      $data[$key] = array();
    }

    foreach($report as $entry)
    {
      foreach($keys as $key)
      {
        $data[$key][] = $entry->get($key);
      }
    }
    
    return $data;
  }
}