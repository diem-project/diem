<?php

class dmUserLogActions extends dmAdminBaseActions
{
	
	public function executeClear(dmWebRequest $request)
	{
    $log = new dmUserLog;
		$log->clear();
		$this->getUser()->logInfo(dm::getI18n()->__('User log cleared'));
		return $this->redirect('dmUserLog/index');
	}
  
	public function executeRefresh(dmWebRequest $request)
	{
		$log = new dmUserLog;

		$responseHash = md5(dmArray::first($log->getEntries(1))->toJson());
		
		if ($responseHash == $request->getParameter('hash'))
		{
			return $this->renderText('-');
		}
		
    $viewClass = 'dmUserLogView'.dmString::camelize($request->getParameter('view'));
		
		$view = new $viewClass($log, $this->getUser()->getCulture());
		
		return $this->renderText(
		  $view->renderBody($request->getParameter('max', 20)).
		  '__DM_SPLIT__'.
  	  $responseHash
		);
	}
	
  public function executeIndex(dmWebRequest $request)
  {
  	$log = new dmUserLog;
    $this->view = new dmUserLogView($log, $this->getUser()->getCulture());
    $this->filesize = $log->getSize();
  }
  
}