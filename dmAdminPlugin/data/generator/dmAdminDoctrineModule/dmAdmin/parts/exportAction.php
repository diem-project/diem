  public function executeExport(sfWebRequest $request)
  {
    $this->doExport(array(
      'format' => $request->getParameter('format', 'csv'),
      'extension' => $request->getParameter('extension', 'csv'),
      'encoding' => $request->getParameter('encoding', 'utf-8'),
      'exportClass' => '<?php echo $this->getModule()->getModel() ?>AdminExport',
      'module' => $this->getDmModule()
    ));
  }