<?php

class dmLogChart extends dmChart
{
  protected
  $eventsFilter = array(
    'clear cache'
  );
  
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

    $dataSet->SetSerieName($this->getI18n()->__('Requests per minute'), "nbReq");
    $dataSet->SetSerieName($this->getI18n()->__('Errors per minute'), "nbErr");
    $dataSet->SetSerieName($this->getI18n()->__('Latency in ms'), "time");
    $dataSet->SetSerieName($this->getI18n()->__('Memory used in %'), "mem");
    
    foreach($this->eventsFilter as $eventType)
    {
      $dataSet->AddPoint($this->data['events'][$eventType], $eventType);
    }

    $dataSet->SetSerieName($this->getI18n()->__('Cache cleared'), 'clear cache');
    
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
    $this->drawRightScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_START0, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 10);
    $this->drawGrid(4,TRUE, self::$colors['grey1'][0], self::$colors['grey1'][1], self::$colors['grey1'][2]);
    $this->drawFilledCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(), 0.2, 10);
//    $this->drawFilledLineGraph($dataSet->GetData(),$dataSet->GetDataDescription(), 0.2, 10);
    
    $this->clearScale();
    $dataSet->removeAllSeries();
    foreach($this->eventsFilter as $eventType)
    {
      $dataSet->addSerie($eventType);
    }
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_START0, 0, 0, 0,false);
    $this->drawStackedBarGraph($dataSet->GetData(), $dataSet->GetDataDescription(), 30, false);
    $this->writeValuesOptions($dataSet->GetData(), $dataSet->GetDataDescription(), 'clear cache', array(
      '>' => 0
    ));

    $this->clearScale();
    $dataSet->removeAllSeries();
    $dataSet->AddSerie("mem");
    $maxMem = 64; //(int) ini_get('memory_limit')
    $this->setFixedScale(0, $maxMem);
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_START0, 0, 0, 0,false,0,0, false, 10);
    $this->drawFilledCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(), 0.2, 20);
//    $this->drawFilledLineGraph($dataSet->GetData(),$dataSet->GetDataDescription(), 10);
    
    $this->clearScale();
    $dataSet->removeAllSeries();
    $dataSet->AddSerie("time");
    $dataSet->SetYAxisName($this->getI18n()->__('Latency in ms'));
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_START0, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 10);
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
      
      $requestLogEntries = $this->serviceContainer->getService('request_log')->getEntries(10000, array(
        'hydrate' => false,
        'keys' => array('time', 'timer', 'code', 'mem')
      ));
      
      $logDelta = dmArray::get(dmArray::first($requestLogEntries), 'time') - dmArray::get(dmArray::last($requestLogEntries), 'time');
      $hours = $logDelta / 3600;
    
      if ($hours < 1)
      {
        throw new dmException('Not enough log entries');
      }
      
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
      
      if (count($requestLogEntries) < 50)
      {
        throw new dmException('Not enough log entries');
      }
      
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
          $nb = count($tmpTimes);
          $trustableData = $nb>=10;
          
          $data['date'][] = $stepDate;
          $data['nbReq'][] = $trustableData ? $nb/$stepFactor : '';
          $data['nbErr'][] = $trustableData ? $tmpErrs/$stepFactor : '';
          $data['time'][] = $trustableData ? array_sum($tmpTimes) / $nb : '';
          $data['mem'][] = $trustableData ? array_sum($tmpMems) / $nb : '';

          $stepDate -= $step;
          $tmpTimes = array($timer);
          $tmpMems = array($mem);
          $tmpErrs  = $err;
        }
      }
      
      $nb = count($tmpTimes);
      $trustableData = $nb>=5;
        
      $data['date'][] = $stepDate;
      $data['nbReq'][] = $nb/$stepFactor;
      $data['nbErr'][] = $tmpErrs/$stepFactor;
      $data['time'][] = $trustableData ? array_sum($tmpTimes) / $nb : "";
      $data['mem'][] = $trustableData ? array_sum($tmpMems) / $nb : "";
      
      foreach(array_keys($data) as $key)
      {
        $data[$key] = array_reverse($data[$key]);
      }
    
      foreach($data['mem'] as $index => $value)
      {
        $data['nbReq'][$index] = $data['nbReq'][$index] / 60;
        
        $data['nbErr'][$index] = $data['nbErr'][$index] / 60;
          
        if ("" != $data['mem'][$index])
        {
          $data['mem'][$index] = $value / (1024*1024);
        }

        if($data['time'][$index] > 5000)
        {
          $data['time'][$index] = 5000;
        }
      }
      
      $events = $this->serviceContainer->getService('event_log')
      ->getEntries(1000, array('filter' => array($this, 'filterEvent'), 'hydrate' => false));
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
        
        if($timeDelta < ($data['date'][1] - $data['date'][0]))
        {
          ++$data['events'][$eventType][$nearestTimeIndex];
        }
      }
      
      unset($events);
      
      $this->setCache('data', $data);
    }
    
    return $data;
  }
  
  public function filterEvent(array $data)
  {
    return isset($data['action']) && in_array($data['action'].' '.$data['type'], $this->eventsFilter);
  }

}