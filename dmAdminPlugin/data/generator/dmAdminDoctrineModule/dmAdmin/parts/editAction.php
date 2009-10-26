  public function executeEdit(sfWebRequest $request)
  {
    $this-><?php echo $this->getSingularName() ?> = $this->getObjectOrForward404($request);
    
    $this->form = $this->configuration->getForm($this-><?php echo $this->getSingularName() ?>);

    $this->nearRecords = $this-><?php echo $this->getSingularName() ?>->getPrevNextRecords($this->buildQuery());
  }
