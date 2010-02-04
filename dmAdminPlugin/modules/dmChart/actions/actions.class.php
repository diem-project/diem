<?php

class dmChartActions extends dmAdminBaseActions
{
  
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
        $this->getHelper()->link('@dm_chart?name='.$chartKey)
        ->text($this->image->htmlWidth('100%'))
        ->title($this->getI18n()->__('Expanded view'))
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
    $this->charts = array();

    foreach($this->getServiceContainer()->getServiceIds() as $serviceId)
    {
      if (substr($serviceId, -6) === '_chart')
      {
        $reflection = new ReflectionClass($this->getServiceContainer()->getParameter($serviceId.'.class'));
        
        if ($reflection->isSubClassOf('dmChart'))
        {
          $this->charts[substr($serviceId, 0, strlen($serviceId)-6)] = $this->getServiceContainer()->getParameter($serviceId.'.options');
        }
      }
    }
    
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
    $this->getServiceContainer()->mergeParameter($serviceName.'.options', $options);
    
    $this->chart = $this->getService($serviceName);
    
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