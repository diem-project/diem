<?php

class dmGapiReportEntry extends gapiReportEntry
{
  
  public function getMetric($name)
  {
    return $this->metrics[$name];
  }
  
  public function getDimension($name)
  {
    return $this->dimensions[$name];
  }
  
  public function get($name)
  {
    if (isset($this->metrics[$name]))
    {
      return $this->metrics[$name];
    }
    elseif (isset($this->dimensions[$name]))
    {
      return $this->dimensions[$name];
    }

    throw new Exception('No valid metric or dimension called "' . $name . '"');
  }
  
}