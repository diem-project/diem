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
        $chart = $sc->getService($serviceId);
        
        if ($chart instanceof dmChart)
        {
          $charts[substr($serviceId, 0, strlen($serviceId)-6)] = $chart;
        }
      }
    }
    
    return $charts;
  }
  
  public function executeImage(dmWebRequest $request)
  {
    $chartKey = $request->getParameter('name');
    
    $this->tryToGetChartImage($chartKey.'_chart', array(
      'width' => 500,
      'height' => 300
    ));
    
    if ($this->image)
    {
      return $this->renderText(
        $this->context->getHelper()->£link('@dm_chart?name='.$chartKey)
        ->text($this->image->htmlWidth('100%'))
        ->title($this->context->getI18n()->__('Expanded view'))
        ->set('.block')
      );
    }
    else
    {
      return $this->renderPartial($this->chart instanceof dmGaChart ? 'gaError' : 'error');
    }
  }
  
  public function executeIndex(dmWebRequest $request)
  {
    $this->charts = $this->getCharts();
    
    $this->selectedIndex = array_search($request->getParameter('name'), array_keys($this->charts));
  }
  
  public function executeShow(dmWebRequest $request)
  {
    $chartKey = $request->getParameter('name');
    
    $this->tryToGetChartImage($chartKey.'_chart', array(
      'width' => 1000,
      'height' => 500
    ));
  }

  protected function tryToGetChartImage($serviceName, array $options = array())
  {
    $this->context->getServiceContainer()->mergeParameter($serviceName.'.options', $options);
    
    $this->chart = $this->context->get($serviceName);
    
    if (!$this->chart->isAvailable())
    {
      return false;
    }
    
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