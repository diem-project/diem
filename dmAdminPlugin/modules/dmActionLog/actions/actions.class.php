<?php

class dmActionLogActions extends dmAdminBaseActions
{
  
  public function preExecute()
  {
    $this->log = $this->dmContext->getService('action_log');
  }
  
  public function executeClear(dmWebRequest $request)
  {
    $this->log->clear();
    $this->getUser()->logInfo($this->context->getI18n()->__('Action log cleared'));
    return $this->redirect('dmActionLog/index');
  }
  
  public function executeRefresh(dmWebRequest $request)
  {
    $responseHash = $this->log->getStateHash();
    
    if ($responseHash == $request->getParameter('hash'))
    {
      return $this->renderText('-');
    }
    
    $viewClass = 'dmActionLogView'.dmString::camelize($request->getParameter('view'));
    
    $view = new $viewClass($this->log, $this->getUser()->getCulture());
    
    return $this->renderText(
      $view->renderBody($request->getParameter('max', 20)).
      '__DM_SPLIT__'.
      $responseHash
    );
  }
  
  public function executeIndex(dmWebRequest $request)
  {
    $this->view = new dmActionLogView($this->log, $this->getUser()->getCulture());
    $this->filesize = $this->log->getSize();
  }
  
}