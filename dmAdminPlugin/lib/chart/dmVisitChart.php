<?php

class dmVisitChart extends dmGaChart
{
  protected function draw()
  {
    $this->choosePalette('diem');
    $this->setColorPalette(1, 140, 140, 200);
    $this->setColorPalette(0, 140, 200, 140);
    
    $dataSet = new dmChartData;
    $dataSet->AddPoint($this->data['pageviews'], 'pageviews');
    $dataSet->AddPoint($this->data['visitors'], 'visitors');
    $dataSet->AddPoint($this->data['dates'], 'dates');
    $dataSet->SetAbsciseLabelSerie("dates");
    $dataSet->SetSerieName("Pages / month", "pageviews");
    $dataSet->SetSerieName("Visitors / month", "visitors");

    // Prepare the graph area
    $this->setGraphArea(80, 10, $this->getWidth()-80, $this->getHeight()-20);
    $this->drawGraphArea(255, 255, 255);
  
    // Draw the pageviews graph
    $dataSet->AddSerie("pageviews");  
    $dataSet->SetYAxisName("Pages");
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_START0, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 3);
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
    $this->drawRightScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_START0, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 3);
    $this->setShadowProperties(3,3,0,0,0,30,4);
    $this->drawCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription());
    $this->clearShadow();
    $this->drawFilledCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(),.1,20);
    $this->drawPlotGraph($dataSet->GetData(),$dataSet->GetDataDescription(),3,2,255,255,255);

    // Finish the graph
    $this->drawLegend(85,5,$dataSet->GetDataDescription(),255,255,255);
  }

  protected function getData()
  {
    if (!$data = $this->getCache('data'))
    {
      $report = $this->gapi->getReport(array(
        'dimensions'  => array('month', 'year'),
        'metrics'     => array('pageviews', 'visits')
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
        $data['visitors'][] = $entry->get('visits');
      }
      $this->setCache('data', $data);
    }
    
    return $data;
  }

}