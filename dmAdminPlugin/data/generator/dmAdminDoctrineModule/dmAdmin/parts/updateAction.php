  public function executeUpdate(sfWebRequest $request)
  {
    $this-><?php echo $this->getSingularName() ?> = $this->getRoute()->getObject();
    $this->form = $this->configuration->getForm($this-><?php echo $this->getSingularName() ?>);

    $this->nearRecords = $this-><?php echo $this->getSingularName() ?>->getPrevNextRecords($this->buildQuery());

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }
