<?php

class dmBrowserChart extends dmGaChart
{
  
  protected function draw()
  {
    $dataSet = new dmChartData;
    $dataSet->AddPoint($this->data['name']['pageviews'], 'namePageviews');
    $dataSet->AddPoint($this->data['name']['browser'], 'nameBrowser');
    $dataSet->AddPoint($this->data['ieVersion']['pageviews'], 'ieVersionPageviews');
    $dataSet->AddPoint($this->data['ieVersion']['browserVersion'], 'ieVersionBrowserVersion');
    $dataSet->SetSerieName('Pageviews', 'namePageviews');
    $dataSet->SetSerieName('Browser', 'nameBrowser');
    $dataSet->SetSerieName('Pageviews', 'ieVersionPageviews');
    $dataSet->SetSerieName('IE version', 'ieVersionBrowserVersion');
    
    $dataSet->addSerie('namePageviews');
    $dataSet->addSerie('nameBrowser');
    $dataSet->SetAbsciseLabelSerie("nameBrowser");
    
    // Prepare the graph area
    $this->setGraphArea(0, 0, $this->getWidth(), $this->getHeight());
    
    // Draw the pie chart
    $radius = $this->getHeight()/1.9;
    $this->drawPieGraph($dataSet->GetData(),$dataSet->GetDataDescription(),$radius*1.2, $radius/1.25, $radius,PIE_PERCENTAGE,TRUE,70,40,8);
    $this->drawPieLegend($this->getWidth()-90, 5,$dataSet->GetData(),$dataSet->GetDataDescription(),250,250,250);
    
    $this->choosePalette(6);
    
    $dataSet->removeAllSeries();
    $dataSet->addSerie('ieVersionPageviews');
    $dataSet->addSerie('ieVersionBrowserVersion');
    $dataSet->SetAbsciseLabelSerie("ieVersionBrowserVersion");
    
    // Draw the pie chart
    $radius = $this->getHeight()/5;
    $this->drawPieGraph($dataSet->GetData(),$dataSet->GetDataDescription(), $this->getWidth()-$radius*1.5, $this->getHeight()-$radius*1.3-15, $radius,PIE_PERCENTAGE,TRUE,70,20,5);
    $this->drawTitle($this->getWidth()-120, $this->getHeight()-10, 'Internet Explorer', self::$colors['grey3'][0], self::$colors['grey3'][1], self::$colors['grey3'][2]);
    
    $data = $dataSet->getData();
    foreach($data as $index => $value)
    {
      if(!isset($value['ieVersionBrowserVersion']))
      {
        unset($data[$index]);
      }
    }
    
    $this->drawPieLegend($this->getWidth()-90, $this->getHeight()/3,$data,$dataSet->GetDataDescription(),250,250,250);
    
  }

  protected function getData()
  {
    if (!$data = $this->getCache('data'))
    {
      $months = 1;
      
      $startDate = date('Y-m-d', strtotime($months.' month ago'));
      
      $report = $this->gapi->getReport(array(
        'dimensions'  => array('browser'),
        'metrics'     => array('pageviews'),
        'sort_metric' => '-pageviews',
        'start_date'  => $startDate
      ));
      
      $totalPageviews = 0;
      foreach($report as $key => $entry)
      {
        $totalPageviews += $entry->get('pageviews');
      }
      
      $minPageViews = $totalPageviews / 200;
      
      foreach($report as $key => $entry)
      {
        if($entry->get('pageviews') < $minPageViews)
        {
          unset($report[$key]);
        }
      }
      
      $data = array('name' => $this->reportToData($report, array('browser', 'pageviews')));
      
      $data['name']['browser'][array_search('Internet Explorer', $data['name']['browser'])] = 'IE';
      
      $report = $this->gapi->getReport(array(
        'dimensions'  => array('browserVersion'),
        'metrics'     => array('pageviews'),
        'sort_metric' => 'browserVersion',
        'filter'      => 'browser == Internet Explorer',
        'start_date'  => $startDate
      ));
      
      $data['ieVersion'] = $this->reportToData($report, array('browserVersion', 'pageviews'));
      
      foreach($data['ieVersion']['browserVersion'] as $index => $value)
      {
        $data['ieVersion']['browserVersion'][$index] = 'IE '.$value;
      }
      
      $this->setCache('data', $data);
    }
    
    return $data;
  }

}