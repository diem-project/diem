<?php

class dmContentChart extends dmChart
{
  protected
  $modules;
  
  protected function draw()
  {
    for($it=0;$it<2;$it++)
    {
      $this->Palette = array_merge($this->Palette, $this->Palette);
    }
    
    $dataSet = new dmChartData;
    
    foreach($this->getModules() as $moduleKey => $module)
    {
      $dataSet->AddPoint($this->data['modules'][$moduleKey], $moduleKey);
      $dataSet->SetSerieName($this->getI18n()->__($module->getPlural()), $moduleKey);
      $dataSet->AddSerie($moduleKey);
    }
    
    $dataSet->AddPoint($this->data['dates'], 'dm_chart_date');
    $dataSet->SetAbsciseLabelSerie('dm_chart_date');
    
    // Prepare the graph area
    $this->setGraphArea(40, 0, $this->getWidth(), $this->getHeight()-20);
    
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_ADDALLSTART0,self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,2,TRUE, 3);
    $this->drawGrid(4,TRUE,self::$colors['grey1'][0], self::$colors['grey1'][1], self::$colors['grey1'][2],50);
  
    // Draw the 0 line
//    $this->drawTreshold(0,143,55,72,TRUE,TRUE);
    
    // Draw the bar graph  
    $this->drawStackedBarGraph($dataSet->GetData(), $dataSet->GetDataDescription(),TRUE);
  
    // Finish the graph
    $this->drawLegend(45,0,$dataSet->GetDataDescription(),255,255,255);
  }
  
  protected function getModules()
  {
    if (null === $this->modules)
    {
      $modules = array();
      foreach($this->serviceContainer->getService('module_manager')->getProjectModules() as $module)
      {
        if ($module->hasModel() && $module->getTable()->hasField('created_at') && $module->getTable()->createQuery()->count())
        {
          $modules[$module->getKey()] = $module;
        }
      }
      
      $modules = array_merge($modules, $this->getInternalModules());

      $this->modules = $this->serviceContainer->getService('module_manager')->keysToModules(
        $this->serviceContainer->getService('dispatcher')->filter(
          new sfEvent($this, 'dm.content_chart.filter_modules', array('modules' => $modules)),
          array_keys($modules)
        )->getReturnValue()
      );
    }
    
    return $this->modules;
  }

  protected function getData()
  {
    if (!$data = $this->getCache('data'))
    {
      $data = array(
        'date' => array(),
        'modules' => array()
      );
      
      foreach($this->getModules() as $moduleKey => $module)
      {
        $data['modules'][$moduleKey] = array();
      }
      
      for($weekDelta = 12; $weekDelta >= 0; $weekDelta--)
      {
        $data['dates'][] = date('d/m/Y', strtotime($weekDelta.' week ago'));
        
        foreach($this->getModules() as $moduleKey => $module)
        {
          $data['modules'][$moduleKey][] = $this->getNbRecordsForModuleAndWeekDelta($module, $weekDelta);
        }
      }
      
      $this->setCache('data', $data);
    }
    
    return $data;
  }
  
  protected function getInternalModules()
  {
    $modules = $this->serviceContainer->getService('module_manager')->keysToModules(
      array('dmSentMail', 'dmUser')
    );
    
    foreach($modules as $key => $module)
    {
      if (!$module->getTable()->createQuery()->count())
      {
        unset($modules[$key]);
      }
    }
    
    return $modules;
  }
  
  protected function getNbRecordsForModuleAndWeekDelta(dmModule $module, $weekDelta)
  {
    return $module->getTable()->createQuery('r')
    ->where('r.created_at > ?', date('Y-m-d H:i:s', strtotime(($weekDelta+1).' week ago')))
    ->andWhere('r.created_at <= ?', date('Y-m-d H:i:s', strtotime($weekDelta.' week ago')))
    ->count();
  }

}