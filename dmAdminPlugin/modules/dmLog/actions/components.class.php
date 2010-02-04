<?php

class dmLogComponents extends dmAdminBaseComponents
{
  
  public function executeLittle()
  {
    $this->logKey = $this->name;
    
    $this->log = $this->getService($this->name.'_log');
    
    $this->logView = $this->getServiceContainer()
    ->setParameter('log_view.class', get_class($this->log).'ViewLittle')
    ->setParameter('log_view.log', $this->log)
    ->getService('log_view');
  }
  
}