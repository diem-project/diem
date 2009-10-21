<?php

class dmLogChart extends dmChart
{

  protected function configure()
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
    $dataSet->SetAbsciseLabelSerie('date');
//    $dataSet->SetXAxisFormat("date");
    $dataSet->SetSerieName("Requests/H", "nbReq");
    $dataSet->SetSerieName("Errors/H", "nbErr");
    $dataSet->SetSerieName("Latency in s", "time");
    $dataSet->SetSerieName("Memory used %", "mem");

    // Prepare the graph area
    $this->setGraphArea(20, 10, $this->getWidth()-30, $this->getHeight()-20);
    $this->drawGraphArea(255, 255, 255);
    $dataSet->AddSerie("date");
//    $this->drawCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(), .3);

    $this->clearScale();
    $dataSet->removeAllSeries();
    $dataSet->AddSerie("nbReq");
    $dataSet->AddSerie("nbErr");
    $this->drawRightScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 8);
    $this->drawGrid(4,TRUE, self::$colors['grey1'][0], self::$colors['grey1'][1], self::$colors['grey1'][2]);
    $this->drawFilledCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(), 0.2, 10); 

    $this->clearScale();
    $dataSet->removeAllSeries();
    $dataSet->AddSerie("time");
    $dataSet->SetYAxisName("Latency in s");
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, self::$colors['grey2'][0], self::$colors['grey2'][1], self::$colors['grey2'][2],TRUE,0,0, false, 8);
    $this->drawFilledCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(), 0.2, 20);

    $this->clearScale();
    $dataSet->removeAllSeries();
    $dataSet->AddSerie("mem");
    $this->setFixedScale(0, (int) ini_get('memory_limit'));
    $this->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL, 0, 0, 0,false,0,0, false, 8);
    $this->drawFilledCubicCurve($dataSet->GetData(),$dataSet->GetDataDescription(), 0.2, 20);
    
    // Finish the graph
    $this->drawLegend(45,5,$dataSet->GetDataDescription(),255,255,255);
  }

  protected function getData()
  {
    if (!$data = $this->serviceContainer->getService('cache_manager')->getCache('chart/data')->get('log'))
    {
      $data = array(
        'date' => array(),
        'timer' => array()
      );
  
      $nbSteps = 0;
      $nbStepMax = 8*10;
      $stepMix = 3;
      $step = 60*60*$stepMix;
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
      
      foreach($this->serviceContainer->getService('user_log')->getEntries(5000) as $userLogEntry)
      {
        $date = $userLogEntry->get('time');
        $timer = $userLogEntry->get('timer');
        $mem = $userLogEntry->get('mem');
        $err = in_array($userLogEntry->get('code'), array('500', '404'));
        
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
            $data['nbReq'][] = $nb/3;
            $data['nbErr'][] = $tmpErrs/3;
            $data['time'][] = array_sum($tmpTimes) / $nb;
            $data['mem'][] = array_sum($tmpMems) / $nb;
          }
          if (++$nbSteps == $nbStepMax)
          {
            $tmpTimes = array();
            break;
          }
          else
          {
            $stepDate -= $step;
            $tmpTimes = array($timer);
            $tmpMems = array($mem);
            $tmpErrs  = $err;
          }
        }
      }
    
      if($nb = count($tmpTimes))
      {
        $data['date'][] = $stepDate;
        $data['nbReq'][] = $nb/3;
        $data['nbErr'][] = $tmpErrs/3;
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
        
        $data['time'][$index] = $data['time'][$index] / 1000;
        
        $data['date'][$index] = date('d/m', $data['date'][$index]);
      }
      
      $this->serviceContainer->getService('cache_manager')->getCache('chart/data')->set('log', $data);
    }

    return $data;
  }

}