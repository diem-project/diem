<?php

abstract class dmGaChart extends dmChart
{
  protected
  $gapi;
  
  protected function setup()
  {
    try
    {
      $this->gapi = $this->serviceContainer->getGapi();
    }
    catch(Exception $e)
    {
      $this->available = false;
      
      if (sfConfig::get('dm_debug'))
      {
        throw $e;
      }
    }
    parent::setup();
  }
  
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