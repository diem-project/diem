<?php

class dmLogActions extends dmAdminBaseActions
{
  protected function getLogs()
  {
    $logs = array();
    
    $sc = $this->context->getServiceContainer();
    
    foreach($sc->getServiceIds() as $serviceId)
    {
      if (substr($serviceId, -4) === '_log')
      {
        $log = $sc->getService($serviceId);
        
        if($log instanceof dmLog)
        {
          $logs[substr($serviceId, 0, strlen($serviceId)-4)] = $log;
        }
      }
    }
    
    return $logs;
  }
  
  public function executeIndex(dmWebRequest $request)
  {
    $this->logs = $this->getLogs();
    
    $this->selectedIndex = array_search($request->getParameter('name'), array_keys($this->logs));
  }
  
  public function executeShow(dmWebRequest $request)
  {
    $this->forward404Unless(
      $this->log = $this->context->get($request->getParameter('name').'_log')
    );
    
    $this->logView = $this->getServiceContainer()
    ->setParameter('log_view.class', get_class($this->log).'View')
    ->setParameter('log_view.log', $this->log)
    ->getService('log_view')
    ->setMax(200);
  }
  
  public function executeClear(dmWebRequest $request)
  {
    $this->forward404Unless(
      $this->log = $this->context->get($request->getParameter('name').'_log')
    );
    
    $this->log->clear();
    $this->getUser()->logInfo($this->context->getI18n()->__('Log cleared'));
    
    return $this->redirect('dmLog/index');
  }
  
  public function executeRefresh(dmWebRequest $request)
  {
    $data = array();
    
    $nbEntries = array(
      'request' => 8,
      'event'   => 5
    );
    
    foreach(array('request', 'event') as $logKey)
    {
      $log = $this->context->get($logKey.'_log');
      
      $view = $this->getServiceContainer()
      ->setParameter('log_view.class', get_class($log).'ViewLittle')
      ->setParameter('log_view.log', $log)
      ->getService('log_view')
      ->setMax($nbEntries[$logKey]);
      
      $hash = $view->getHash();
      
      if ($hash != $request->getParameter($logKey{0}.'h'))
      {
        $data[$logKey] = array(
          'hash' => $hash,
          'html' => $view->renderBody($nbEntries)
        );
      }
    }
    
    return $this->renderJson($data);
  }
  
}