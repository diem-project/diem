<?php

class dmChartComponents extends dmAdminBaseComponents
{

  protected function tryToGetChartImage($serviceName)
  {
    $this->context->getServiceContainer()->mergeParameter($serviceName.'.options', array(
      'width' => 500,
      'height' => 300
    ));
    
    $this->chart = $this->context->get($serviceName);
    
    try
    {
      $this->image = $this->chart->getImage();
    }
    catch(Exception $e)
    {
      if (sfConfig::get('dm_debug'))
      {
        throw $e;
      }
      
      $this->image = false;
      
      return false;
    }
  }
  
  public function executeLittle()
  {
    $this->chartKey = $this->name;
    $this->tryToGetChartImage($this->name.'_chart');
  }
}