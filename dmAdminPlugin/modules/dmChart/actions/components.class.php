<?php

class dmChartComponents extends dmAdminBaseComponents
{
  
  public function executeLittle()
  {
    $this->chartKey = $this->name;
    
    $this->options = $this->getServiceContainer()->getParameter($this->name.'_chart.options');
  }
}