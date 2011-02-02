  public function executeNew(sfWebRequest $request)
  {
    $this->form = $this->configuration->getForm();
    
    foreach($request->getParameter('defaults', array()) as $key => $value)
    {
      $this->form->setDefault($key, $value);
    }

    $this-><?php echo $this->getSingularName() ?> = $this->form->getObject();
    
    $this->nearRecords = null;
  }
