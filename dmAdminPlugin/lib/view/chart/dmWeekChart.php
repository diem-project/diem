<?php

class dmWeekChart extends dmGaChart
{
  protected function configure()
  {
    $this->choosePalette('diem');
    $this->setColorPalette(1, 40, 60, 200);
    $this->setColorPalette(0, 140, 200, 140);
    $this->setColorPalette(2, 200, 40, 40);
    
    $dataSet = new dmChartData;
    $dataSet->AddPoint($this->data['pageviews'], 'pageviews');
    $dataSet->AddPoint($this->data['visitors'], 'visitors');
    $dataSet->AddPoint($this->data['bounces'], 'bounces');
//    $dataSet->AddPoint($this->data['pagesPerVisitor'], 'pagesPerVisitor');
    $dataSet->AddPoint($this->data['date'], 'date');
    $dataSet->SetAbsciseLabelSerie("date");
    $dataSet->SetSerieName("Pages", "pageviews");
    $dataSet->SetSerieName("Visitors", "visitors");
    $dataSet->SetSerieName("Bounces", "bounces");
//    $dataSet->SetSerieName("per Visitor", "pagesPerVisitor");
    
    // Prepare the graph area
    $this->setGraphArea(40, 10, $this->getWidth()-40, $this->getHeight()-20);
    $this->drawGraphArea(255, 255, 255);
  
    $dataSet->AddSerie("visitors"); 
    $dataSet->AddSerie("bounces"); 
    $dataSet->SetYAxisName("visitors");
    $this->setLineStyle(1, 6);
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 1);
    $this->drawGrid(4,TRUE, self::$colors['grey1'][0], self::$colors['grey1'][1], self::$colors['grey1'][2]);
    $this->drawLineGraph($dataSet->GetData(),$dataSet->GetDataDescription());
    $this->drawPlotGraph($dataSet->GetData(),$dataSet->GetDataDescription(),3,2,255,255,255);
    
    
    $this->drawArea($dataSet->GetData(),"visitors","bounces",self::$colors['blue'][0], self::$colors['blue'][1], self::$colors['blue'][2], 50);
  
//    $this->clearScale();
//    $dataSet->removeAllSeries(); 
//    $dataSet->AddSerie("Visits");  
//    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 1);
//    $this->drawFilledLineGraph($dataSet->GetData(),$dataSet->GetDataDescription(), 10);
//    $this->drawPlotGraph($dataSet->GetData(),$dataSet->GetDataDescription(),3,2,255,255,255);
    
//    $this->clearScale();
//    
//    $dataSet->removeAllSeries();
//    $dataSet->AddSerie("pageviews");  
//    $dataSet->SetYAxisName("pageviews");
//    $this->drawRightScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 1);
//    $this->drawFilledLineGraph($dataSet->GetData(),$dataSet->GetDataDescription(), 10);
//    $this->drawPlotGraph($dataSet->GetData(),$dataSet->GetDataDescription(),3,2,255,255,255);
//    
    // Clear the scale
    $this->clearScale();
  
    // Draw the 2nd graph
    $dataSet->removeAllSeries();
    $dataSet->AddSerie("pageviews");
    $this->setLineStyle(1, 6);
    $this->setLineStyle(1, 1);
    $this->drawRightScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 1);
//    $this->drawCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(), .1);
//    $this->drawPlotGraph($dataSet->GetData(),$dataSet->GetDataDescription(),3,2,255,255,255);
    $this->drawBarGraph($dataSet->GetData(),$dataSet->GetDataDescription(),false, 30); 
    
    // Clear the scale
//    $this->clearScale();
  
    // Draw the 2nd graph
//    $dataSet->removeAllSeries();
//    $dataSet->AddSerie("pagesPerVisitor");
//    $this->setLineStyle(1, 1);
//    $this->drawRightScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 1);
//    $this->drawLineGraph($dataSet->GetData(),$dataSet->GetDataDescription());

    // Finish the graph
    $dataSet->addAllSeries();
    $dataSet->RemoveSerie('date');
    $this->drawLegend(45,5,$dataSet->GetDataDescription(),255,255,255);
  }

  protected function getData()
  {
    $report = $this->serviceContainer->getGapi()->getReport(array(
      'dimensions'  => array('day', 'month', 'date'),
      'metrics'     => array('pageviews', 'visitors', 'bounces'),
      'sort_metric' => 'date',
      'start_date'  => date('Y-m-d',strtotime('10 days ago')),
      'end_date'  => date('Y-m-d',strtotime('1 day ago'))
    ));
    
    $data = $this->reportToData($report, array(
      'date',
      'pageviews',
      'visitors',
      'bounces'
    ));
    
//    $data['pagesPerVisitor'] = array();
//    foreach($data['pageviews'] as $index => $pageviews)
//    {
//      $data['pagesPerVisitor'][$index] = $pageviews/$data['visitors'][$index];
//    }
    
    return $data;
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
        if ('date' === $key)
        {
          $value = $entry->get('day').'/'.$entry->get('month');
        }
        else
        {
          $value = $entry->get($key);
        }
        
        $data[$key][] = $value;
      }
    }
    
    return $data;
  }

}