<?php

class dmBrowserChart extends dmGaChart
{
  
  protected function configure()
  {
    $dataSet = new dmChartData;
    $dataSet->AddPoint($this->data['pageviews'], 'pageviews');
    $dataSet->AddPoint($this->data['browser'], 'browser');
    $dataSet->addAllSeries();
    $dataSet->SetAbsciseLabelSerie("browser");
    
    // Prepare the graph area
    $this->setGraphArea(0, 0, $this->getWidth(), $this->getHeight());
    
    // Draw the pie chart
    $this->drawPieGraph($dataSet->GetData(),$dataSet->GetDataDescription(),$this->getWidth()/3,$this->getHeight()/2.5, $this->getHeight()/2,PIE_PERCENTAGE,TRUE,70,40,8);
    $this->drawPieLegend($this->getWidth()-120,15,$dataSet->GetData(),$dataSet->GetDataDescription(),250,250,250);
  }

  protected function getData()
  {
    $totalPageviews = $this->serviceContainer->getGapi()->getTotalPageViews();
    
    $report = $this->serviceContainer->getGapi()->getReport(array(
      'dimensions'  => array('browser'),
      'metrics'     => array('pageviews'),
      'sort_metric' => '-pageviews',
      'filter'      => 'pageviews > '.round($totalPageviews / 100)
    ));
    
    $data = $this->reportToData($report, array('browser', 'pageviews'));
    
    $data['browser'][array_search('Internet Explorer', $data['browser'])] = 'IE';
    
    return $data;
  }

}