  public function executeSortTable(sfWebRequest $request)
  {
    $this->forward404Unless($this->getDmModule()->getTable()->isSortable());
    
    $this->dmContext->getServiceContainer()->addParameters(array(
      'admin_sort_form.defaults'  => array(),
      'admin_sort_form.options'   => array(
        'module' => $this->getDmModule(),
        'query'  => $this->getSortTableQuery()
      )
    ));
    
    $this->form = $this->dmContext->getServiceContainer()->getService('admin_sort_table_form');
    
    $this->processSortForm($this->form);
  }
  
  protected function getSortTableQuery()
  {
    return $this->getDmModule()->getTable()->createQuery('r')
    <?php $this->getModule()->getTable()->hasField('is_active') && print '->whereIsActive(true)' ?>
    ->orderBy('r.position asc');
  }
  
  public function executeSortReferers(sfWebRequest $request)
  {
    $this->forward404Unless($record = $this->getDmModule()->getTable()->find($request->getParameter('id')));
    
    $this->forward404Unless($refererModule = dmModuleManager::getModuleOrNull($request->getParameter('refererModule')));
    
    $this->forward404Unless($refererModule->getTable()->isSortable());
    
    $this->dmContext->getServiceContainer()->addParameters(array(
      'admin_sort_form.defaults'  => array(),
      'admin_sort_form.options'   => array(
        'module'        => $refererModule,
        'parentRecord'  => $record,
        'query'         => $this->getSortReferersQuery($refererModule)
      )
    ));
    
    $this->form = $this->dmContext->getServiceContainer()->getService('admin_sort_referers_form');
    
    $this->processSortForm($this->form);
  }
  
  protected function getSortReferersQuery(dmModule $refererModule)
  {
    return $refererModule->getTable()->createQuery('r')
    ->whereIsActive(true, $refererModule->getModel())
    ->orderBy('r.position asc');
  }