<?php

class dmVisitChart extends dmGaChart
{
  protected function configure()
  {
    $dataSet = new dmChartData;
    $dataSet->AddPoint($this->data['pages'], 'Pages');
    $dataSet->AddPoint($this->data['visitors'], 'Visitors');
    $dataSet->AddPoint($this->data['dates'], 'Dates');
    $dataSet->SetAbsciseLabelSerie("Dates");
    $dataSet->SetSerieName("Pages", "Pages");
    $dataSet->SetSerieName("Visitors", "Visitors");

    // Prepare the graph area
    $this->setGraphArea(40, 10, $this->getWidth()-40, $this->getHeight()-20);
  
    // Draw the pages graph
    $dataSet->AddSerie("Pages");  
    $dataSet->SetYAxisName("Pages");
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 3);
    $this->drawGraphArea(255, 255, 255);
    $this->drawGrid(4,TRUE, self::$colors['grey1'][0], self::$colors['grey1'][1], self::$colors['grey1'][2]);
    $this->setShadowProperties(3,3,0,0,0,30,4);
    $this->drawCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription());
    $this->clearShadow();
    $this->drawFilledCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(),.1,30);
    $this->drawPlotGraph($dataSet->GetData(),$dataSet->GetDataDescription(),3,2,255,255,255);
    
    // Clear the scale
    $this->clearScale();
  
    // Draw the 2nd graph
    $dataSet->RemoveSerie("Pages");
    $dataSet->RemoveSerie("Dates");
    $dataSet->AddSerie("Visitors");
    $dataSet->SetYAxisName("Visitors");
    $this->drawRightScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 3);
    $this->setShadowProperties(3,3,0,0,0,30,4);
    $this->drawCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription());
    $this->clearShadow();
    $this->drawFilledCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(),.1,20);
    $this->drawPlotGraph($dataSet->GetData(),$dataSet->GetDataDescription(),3,2,255,255,255);

    // Finish the graph
    $this->drawLegend(45,35,$dataSet->GetDataDescription(),255,255,255);
  }

  protected function getData()
  {
    $report = $this->serviceContainer->getGapi()->getReport(array(
      'dimensions'  => array('month', 'year'),
      'metrics'     => array('pageviews', 'visitors')
    ));
    
    $data = array(
      'dates' => array(),
      'pages' => array(),
      'visitors' => array()
    );

    foreach($report as $entry)
    {
      $data['dates'][] = $entry->get('month').'/'.$entry->get('year');
      $data['pages'][] = $entry->get('pageviews');
      $data['visitors'][] = $entry->get('visitors');
    }
    
    return $data;
  }

}