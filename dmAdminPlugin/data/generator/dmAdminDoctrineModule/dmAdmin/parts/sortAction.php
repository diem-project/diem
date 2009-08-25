  public function executeSort(sfWebRequest $request)
  {
    $this->module  = $this->getDmModule();
    $this->objects = dmDb::query($this->module->getModel())->orderBy('Position')->fetchRecords();
  }