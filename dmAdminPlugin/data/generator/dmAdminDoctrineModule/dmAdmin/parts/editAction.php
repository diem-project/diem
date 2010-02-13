  public function executeEdit(sfWebRequest $request)
  {
    $this-><?php echo $this->getSingularName() ?> = $this->getObjectOrForward404($request);
  
    $this->dispatcher->notify(new sfEvent($this, 'admin.edit_object', array('object' => $this-><?php echo $this->getSingularName() ?>)));
    
    $this->form = $this->configuration->getForm($this-><?php echo $this->getSingularName() ?>);

    $this->nearRecords = $this-><?php echo $this->getSingularName() ?>->getPrevNextRecords($this->buildQuery());
  }
