  public function executeSortLocals(sfWebRequest $request)
  {
    $this->module         = dmModuleManager::getModule('<?php echo $this->getModuleName(); ?>');
    $this->object         = $this->getRoute()->getObject();

    $this->localModule  = dmModuleManager::getModule($request->getParameter('refererModule'));
    $this->localObjects = array(); dmDebug::log('FIXME sortLocals');
  }