<?php

class dmLogChart extends dmChart
{
  protected
  $eventsFilter = array(
    'clear cache',
//    'update sitemap',
//    'update search'
  );

  protected function configure()
  {
//    $this->options['lifetime'] = 1;
  }
  
  protected function draw()
  {
    $this->choosePalette('diem');
    $this->setColorPalette(1, 200, 40, 40);
    $this->setColorPalette(2, 140, 200, 140);
    $this->setColorPalette(0, 40, 60, 200);
    $this->setColorPalette(3, 250, 200, 40);
    
    $dataSet = new dmChartData;
    
    $dataSet->AddPoint($this->data['date'], 'date');
    $dataSet->AddPoint($this->data['time'], 'time');
    $dataSet->AddPoint($this->data['nbReq'], 'nbReq');
    $dataSet->AddPoint($this->data['nbErr'], 'nbErr');
    $dataSet->AddPoint($this->data['mem'], 'mem');

    $dataSet->SetSerieName("Requests / minute", "nbReq");
    $dataSet->SetSerieName("Errors / minute", "nbErr");
    $dataSet->SetSerieName("Latency in ms", "time");
    $dataSet->SetSerieName("Memory used %", "mem");
    
    foreach($this->eventsFilter as $eventType)
    {
      $dataSet->AddPoint($this->data['events'][$eventType], $eventType);
      $dataSet->SetSerieName(dmString::humanize($eventType), $eventType);
    }
    
    $dataSet->SetAbsciseLabelSerie('date');
    $dataSet->SetXAxisFormat('date');
    // Prepare the graph area
    $this->setGraphArea(40, 10, $this->getWidth()-30, $this->getHeight()-20);
    $this->drawGraphArea(255, 255, 255);
    $dataSet->AddSerie("date");
    
    $this->clearScale();
    $dataSet->removeAllSeries();
    $dataSet->AddSerie("nbReq");
    $dataSet->AddSerie("nbErr");
    $this->drawRightScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 10);
    $this->drawGrid(4,TRUE, self::$colors['grey1'][0], self::$colors['grey1'][1], self::$colors['grey1'][2]);
    $this->drawFilledCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(), 0.2, 10);
    
    $this->clearScale();
    $dataSet->removeAllSeries();
    foreach($this->eventsFilter as $eventType)
    {
      $dataSet->addSerie($eventType);
    }
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, 0, 0, 0,false);
    $this->drawStackedBarGraph($dataSet->GetData(), $dataSet->GetDataDescription(), 30, false);

    $this->clearScale();
    $dataSet->removeAllSeries();
    $dataSet->AddSerie("mem");
    $maxMem = 64; //(int) ini_get('memory_limit')
    $this->setFixedScale(0, $maxMem);
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, 0, 0, 0,false,0,0, false, 10);
    $this->drawFilledCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(), 0.2, 20);
    
    $this->clearScale();
    $dataSet->removeAllSeries();
    $dataSet->AddSerie("time");
    $dataSet->SetYAxisName("Latency in s");
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 10);
    $this->drawFilledCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(), 0.2, 30);
    
    // Add labels
//    foreach($this->data['events'] as $event)
//    {
//      $this->setLabel($dataSet->GetData(), $dataSet->GetDataDescription(), 'time', $event['time'], $event['msg'], 221,230,174);
//    }
    
    // Finish the graph
    $this->drawLegend(45,5,$dataSet->GetDataDescription(),255,255,255);
  }

  protected function getData()
  {
    if (!$data = $this->getCache('data'))
    {
      $data = array(
        'date' => array(),
        'timer' => array()
      );
      
      $requestLogEntries = $this->serviceContainer->getService('request_log')->getEntries(0, array(
        'hydrate' => false
      ));
  
      $logDelta = dmArray::get(dmArray::first($requestLogEntries), 'time') - dmArray::get(dmArray::last($requestLogEntries), 'time');
      
      $hours = $logDelta / 3600;
      
      $stepFactor = $hours / 40;
      
      $step = round(60*60*$stepFactor);
      $stepDate = $_SERVER['REQUEST_TIME']-$step;
      $tmpTimes = array();
      $tmpMems = array();
      $tmpErrs = 0;
      $data = array(
        'date' => array(),
        'time' => array(),
        'nbReq'   => array(),
        'nbErr'   => array(),
        'mem'     => array()
      );
      
      foreach($requestLogEntries as $userLogEntry)
      {
        $date = $userLogEntry['time'];
        $timer = $userLogEntry['timer'];
        $mem = $userLogEntry['mem'];
        $err = in_array($userLogEntry['code'], array('500', '404'));
        
        if ($date > $stepDate)
        {
          $tmpTimes[] = $timer;
          $tmpMems[] = $mem;
          $tmpErrs  += $err;
        }
        else
        {
          if($nb = count($tmpTimes))
          {
            $data['date'][] = $stepDate;
            $data['nbReq'][] = $nb/$stepFactor;
            $data['nbErr'][] = $tmpErrs/$stepFactor;
            $data['time'][] = array_sum($tmpTimes) / $nb;
            $data['mem'][] = array_sum($tmpMems) / $nb;
          }
          $stepDate -= $step;
          $tmpTimes = array($timer);
          $tmpMems = array($mem);
          $tmpErrs  = $err;
        }
      }
      
      if($nb = count($tmpTimes))
      {
        $data['date'][] = $stepDate;
        $data['nbReq'][] = $nb/$stepFactor;
        $data['nbErr'][] = $tmpErrs/$stepFactor;
        $data['time'][] = array_sum($tmpTimes) / $nb;
        $data['mem'][] = array_sum($tmpMems) / $nb;
      }
      
      foreach(array_keys($data) as $key)
      {
        $data[$key] = array_reverse($data[$key]);
      }
    
      foreach($data['mem'] as $index => $value)
      {
        $data['mem'][$index] = $value / (1024*1024);
        
        $data['nbReq'][$index] = $data['nbReq'][$index] / 60;
        
        $data['nbErr'][$index] = $data['nbErr'][$index] / 60;
      }
      
      $events = $this->serviceContainer->getService('event_log')
      ->getFilteredEntries(1000, array($this, 'filterEvent'), array('hydrate' => false));
      $data['events'] = array();
      foreach($this->eventsFilter as $eventType)
      {
        $data['events'][$eventType] = array();
        for($it=0,$itMax=count($data['date']); $it<$itMax; $it++)
        {
          $data['events'][$eventType][] = 0;
        }
      }
      
      $time = time();
      foreach($events as $event)
      {
        $eventType = $event['action'].' '.$event['type'];
        $timeDelta = $time;
        $nearestTimeIndex = null;
        foreach($data['date'] as $index => $time)
        {
          $eventTimeDelta = abs($time - $event['time']);
          
          if ($eventTimeDelta < $timeDelta)
          {
            $nearestTimeIndex = $index;
            $timeDelta = $eventTimeDelta;
          }
        }
        
        ++$data['events'][$eventType][$nearestTimeIndex];
      }
      
      unset($events);
      
      $this->setCache('data', $data);
    }
    
    return $data;
  }
  
  public function filterEvent(array $data)
  {
    return in_array($data['action'].' '.$data['type'], $this->eventsFilter);
  }

}