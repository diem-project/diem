<?php

class dmChartActions extends dmAdminBaseActions
{
  protected function getCharts()
  {
    $charts = array();
    
    $sc = $this->context->getServiceContainer();
    
    foreach($sc->getServiceIds() as $serviceId)
    {
      if (substr($serviceId, -6) === '_chart')
      {
        $charts[substr($serviceId, 0, strlen($serviceId)-6)] = $sc->getService($serviceId);
      }
    }
    
    return $charts;
  }
  
  public function executeIndex(dmWebRequest $request)
  {
    $this->charts = $this->getCharts();
    
    $this->selectedChartKey = $request->getParameter('name');
  }
  
  public function executeShow(dmWebRequest $request)
  {
    $chartKey = $request->getParameter('name');
    
    $this->tryToGetChartImage($chartKey.'_chart');
  }

  protected function tryToGetChartImage($serviceName)
  {
    $this->context->getServiceContainer()->mergeParameter($serviceName.'.options', array(
      'width' => 1000,
      'height' => 500
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
}