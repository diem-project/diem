  public function executeEdit(sfWebRequest $request)
  {
    $this-><?php echo $this->getSingularName() ?> = $this->getRoute()->getObject();
    $this->form = $this->configuration->getForm($this-><?php echo $this->getSingularName() ?>);

    $this->nearRecords = $this-><?php echo $this->getSingularName() ?>->getNearRecords($this->buildQuery());
  }
