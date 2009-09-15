  public function executeSort(sfWebRequest $request)
  {
    $this->dmContext->getServiceContainer()->addParameters(array(
      'admin_sort_form.defaults'  => array(),
      'admin_sort_form.options'   => array(
        'module' => $this->getDmModule(),
        'query'  => $this->getSortQuery()
      )
    ));
    
    $this->form = $this->dmContext->getServiceContainer()->getService('admin_sort_form');
    
    if ($this->getRequest()->isMethod('post'))
    {
      $this->form->bind();
      
      if($this->form->isValid())
      {
        try
        {
          $this->form->save();
        }
        catch(Exception $e)
        {
          if ($this->getUser()->can('system'))
          {
            throw $e;
          }
          $this->getUser()->logError($this->context->getI18n()->__('A problem occured when sorting the items'), true);
        }

        $this->getUser()->logInfo($this->context->getI18n()->__('The items have been sorted successfully'), true);
        return $this->redirect('<?php echo $this->getModule()->getKey() ?>/sort');
      }
    }
  }
  
  protected function getSortQuery()
  {
    return $this->getDmModule()->getTable()->createQuery('r')
    <?php $this->getModule()->getTable()->hasField('is_active') && print'->whereIsActive(true)' ?>
    ->orderBy('r.position asc');
  }