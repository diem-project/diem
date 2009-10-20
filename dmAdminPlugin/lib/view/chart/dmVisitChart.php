<?php

class dmVisitChart extends dmGaChart
{
  protected function configure()
  {
    $this->choosePalette('diem');
    $this->setColorPalette(1, 140, 140, 200);
    $this->setColorPalette(0, 140, 200, 140);
    
    $dataSet = new dmChartData;
    $dataSet->AddPoint($this->data['pageviews'], 'pageviews');
    $dataSet->AddPoint($this->data['visitors'], 'visitors');
    $dataSet->AddPoint($this->data['dates'], 'dates');
    $dataSet->SetAbsciseLabelSerie("dates");
    $dataSet->SetSerieName("Pages", "pageviews");
    $dataSet->SetSerieName("Visitors", "visitors");

    // Prepare the graph area
    $this->setGraphArea(40, 10, $this->getWidth()-40, $this->getHeight()-20);
    $this->drawGraphArea(255, 255, 255);
  
    // Draw the pageviews graph
    $dataSet->AddSerie("pageviews");  
    $dataSet->SetYAxisName("Pages");
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 3);
    $this->drawGrid(4,TRUE, self::$colors['grey1'][0], self::$colors['grey1'][1], self::$colors['grey1'][2]);
    $this->setShadowProperties(3,3,0,0,0,30,4);
    $this->drawCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription());
    $this->clearShadow();
    $this->drawFilledCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(),.1,30);
    $this->drawPlotGraph($dataSet->GetData(),$dataSet->GetDataDescription(),3,2,255,255,255);
    
    // Clear the scale
    $this->clearScale();
  
    // Draw the 2nd graph
    $dataSet->RemoveAllSeries();
    $dataSet->AddSerie("visitors");
    $dataSet->SetYAxisName("Visitors");
    $this->drawRightScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 3);
    $this->setShadowProperties(3,3,0,0,0,30,4);
    $this->drawCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription());
    $this->clearShadow();
    $this->drawFilledCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(),.1,20);
    $this->drawPlotGraph($dataSet->GetData(),$dataSet->GetDataDescription(),3,2,255,255,255);

    // Finish the graph
    $this->drawLegend(45,5,$dataSet->GetDataDescription(),255,255,255);
  }

  protected function getData()
  {
    $report = $this->serviceContainer->getGapi()->getReport(array(
      'dimensions'  => array('month', 'year'),
      'metrics'     => array('pageviews', 'visitors')
    ));
    
//    dmDebug::kill($report);
    
    $data = array(
      'dates' => array(),
      'pageviews' => array(),
      'visitors' => array()
    );

    foreach($report as $entry)
    {
      $data['dates'][] = $entry->get('month').'/'.$entry->get('year');
      $data['pageviews'][] = $entry->get('pageviews');
      $data['visitors'][] = $entry->get('visitors');
    }
    
    return $data;
  }

}