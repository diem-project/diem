<?php

class dmContentChart extends dmChart
{
  protected
  $modules;
  
  protected function configure()
  {
    $dataSet = new dmChartData;
    
    foreach($this->getModules() as $moduleKey => $module)
    {
      $dataSet->AddPoint($this->data['modules'][$moduleKey], $moduleKey);
      $dataSet->SetSerieName($module->getPlural(), $moduleKey);
      $dataSet->AddSerie($moduleKey);
    }
    
    $dataSet->AddPoint($this->data['dates'], 'dm_chart_date');
    $dataSet->SetAbsciseLabelSerie('dm_chart_date');
    
    // Prepare the graph area
    $this->setGraphArea(40, 0, $this->getWidth(), $this->getHeight()-20);
    
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_ADDALL,self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,2,TRUE, 3);
    $this->drawGrid(4,TRUE,self::$colors['grey1'][0], self::$colors['grey1'][1], self::$colors['grey1'][2],50);
  
    // Draw the 0 line
    $this->drawTreshold(0,143,55,72,TRUE,TRUE);
    
    // Draw the bar graph  
    $this->drawStackedBarGraph($dataSet->GetData(), $dataSet->GetDataDescription(),TRUE);
  
//    foreach($this->modules as $moduleKey => $module)
//    {
//      // Draw the pages graph
//      $dataSet->removeAllSeries();
//      $dataSet->AddSerie($moduleKey);
//      // Clear the scale
//      $this->clearScale();
//      $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 3);
//      $this->drawCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription());
//      
//    }
  
    // Finish the graph
    $this->drawLegend(45,5,$dataSet->GetDataDescription(),255,255,255);
  }
  
  protected function getModules()
  {
    if (null === $this->modules)
    {
      $this->modules = array();
      foreach($this->serviceContainer->getService('module_manager')->getProjectModules() as $module)
      {
        if ($module->hasModel() && $module->getTable()->hasField('created_at'))
        {
          $this->modules[$module->getKey()] = $module;
        }
      }
      $this->modules = array_merge($this->modules, $this->getInternalModules());
    }
    
    return $this->modules;
  }

  protected function getData()
  {
    if (!$data = $this->serviceContainer->getService('cache_manager')->getCache('chart/data')->get('content'))
    {
      $data = array(
        'date' => array(),
        'modules' => array()
      );
      
      foreach($this->getModules() as $moduleKey => $module)
      {
        $data['modules'][$moduleKey] = array();
      }
      
      for($monthDelta = 12; $monthDelta > 0; $monthDelta--)
      {
        $data['dates'][] = date('m/Y', strtotime($monthDelta.' month ago'));
        
        foreach($this->getModules() as $moduleKey => $module)
        {
          $data['modules'][$moduleKey][] = $this->getNbRecordsForModuleAndMonthDelta($module, $monthDelta);
        }
      }
      
      $this->serviceContainer->getService('cache_manager')->getCache('chart/data')->set('content', $data);
    }
    
    return $data;
  }
  
  protected function getInternalModules()
  {
    return $this->serviceContainer->getService('module_manager')->keysToModules(array(
      'DmError',
      'dmGuardUser'
    ));
  }
  
  protected function getNbRecordsForModuleAndMonthDelta(dmModule $module, $monthDelta)
  {
    return $module->getTable()->createQuery('r')
    ->where('r.created_at > ?', date('Y-m-d H:i:s', strtotime(($monthDelta+1).' month ago')))
    ->andWhere('r.created_at <= ?', date('Y-m-d H:i:s', strtotime($monthDelta.' month ago')))
    ->count();
  }

}