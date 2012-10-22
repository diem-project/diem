  /**
	 * @return dmDoctrinePager
	 */
  public function getPager()
  {
  	if(null == $this->pager)
  	{
	    $this->pager = $this->configuration->getPager('<?php echo $this->getModelClass() ?>');
	    $this->pager->setQuery($this->buildQuery());
	    $this->pager->setPage($this->getPage());
	    $this->pager->init();
		}
    return $this->pager;
  }

  /**
	 * @return boolean
	 */
  public function hasPager()
  {
  	return $this->pager ? true : false;
  }

	/**
	 * @param int $page
	 */
  protected function setPage($page)
  {
    $this->getUser()->setAttribute('<?php echo $this->getModuleName() ?>.page', $page, 'admin_module');
  }

  /**
	 * @return int
	 */
  public function getPage()
  {
    return $this->getUser()->getAttribute('<?php echo $this->getModuleName() ?>.page', 1, 'admin_module');
  }

	/**
	 * @return myDoctrineQuery
	 */ 
  protected function buildQuery()
  {
    $tableMethod = $this->configuration->getTableMethod();
<?php if ($this->configuration->hasFilterForm()): ?>
    if (null === $this->filters)
    {
      $this->filters = $this->configuration->getFilterForm($this->getFilters());
    }

    $this->filters->setTableMethod($tableMethod);
		$this->getDmModule()->getTable()->setOption('admin.query.relations', $this->getRelationsAlias());
    $query = $this->filters->buildQuery($this->getFilters());
<?php else: ?>
    $query = dmDb::table('<?php echo $this->getModelClass() ?>')
      ->createQuery('a');

    if ($tableMethod)
    {
      $query = dmDb::table('<?php echo $this->getModelClass() ?>')->$tableMethod($query);
    }
<?php endif; ?>

    $this->addSearchQuery($query);

    $this->addSortQuery($query);
    
    $this->addRecordPermissionQuery($query);
    
    $event = $this->dispatcher->filter(new sfEvent($this, 'admin.build_query'), $query);
    $query = $event->getReturnValue();

    return $query;
  }
