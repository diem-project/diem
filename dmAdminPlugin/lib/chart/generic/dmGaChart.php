<?php

abstract class dmGaChart extends dmChart
{
  protected
  $gapi;
  
  protected function initialize(array $options)
  {
    try
    {
      $this->gapi = $this->serviceContainer->getService('gapi')->authenticate(null, null, dmConfig::get('ga_token'));
    }
    catch(dmGapiException $e)
    {
      $this->available = false;
      
      if (sfConfig::get('dm_debug'))
      {
        throw $e;
      }
    }
    
    parent::initialize($options);
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