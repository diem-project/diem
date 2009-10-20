<?php

class dmChartComponents extends dmAdminBaseComponents
{
  
  public function executeLittle()
  {
    $this->chartKey = $this->name;
    $this->chart = $this->context->get($this->name.'_chart');
  }
}