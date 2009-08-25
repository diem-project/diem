<?php

class dmFrontModuleComponents extends myFrontBaseComponents
{

	protected
	$dmModule;

  /*
   * @return myDoctrineRecord $record
   */
	protected function getShowRecord(myDoctrineQuery $query = null)
	{
    if (is_null($query))
    {
      $query = $this->getTable()->createQuery('r');
    }
    
		if ($this->recordId)
		{
			$record = $query->addWhere($query->getRootAlias().'.id = ?', $this->recordId)->fetchRecord();

	    if (!$record)
	    {
	      throw new dmException(sprintf('No record found for %s %d', $this->getDmModule(), $this->recordId));
	    }
		}
		else
		{
			$record = $query
	    ->whereDescendantId($this->getPage()->getDmModule()->getModel(), $this->getPage()->recordId, $this->getDmModule()->getModel())
	    ->fetchRecord();
    
//    $this->getDmContext()->getPage()->record->getAncestorRecord($this->getDmModule()->getModel());

			if (!$record)
			{
			  throw new dmException(sprintf('Can not determine auto %s for page %s', $this->getDmModule(), $page));
			}
		}

		return $record;
	}

	/*
	 * @return myDoctrinePager $pager
	 */
	protected function getListPager(myDoctrineQuery $query = null)
	{
		$pager = new myDoctrinePager($this->getDmModule()->getModel(), $this->maxPerPage);

		$pager->setQuery($this->_getListQuery($query));

		$pager->setPage($this->getRequest()->getParameter('page', 1));

		$pager->configureNavigation(array(
		  'top'     => $this->navTop,
		  'bottom'  => $this->navBottom
		));

		$pager->init();

		return $pager;
	}

	protected function _getListQuery(myDoctrineQuery $query = null)
	{
		if (is_null($query))
		{
		  $query = $this->getTable()->createQuery('r');
		}
		
		/*
		 * Apply order
		 */
		if ($this->orderType == 'rand')
		{
			$query->addOrderBy('RAND()');
		}
		else
		{
			$query->addOrderBy($this->orderField.' '.$this->orderType);
		}
    
		/*
		 * Apply filters
		 */
		foreach($this->filters as $filterKey => $filterValue)
		{
      if (($filterModule = $this->dmModule->getAncestor($filterKey)) || ($filterModule = $this->dmModule->getAssociation($filterKey)))
      {
        if ($filterValue)
		    {
		      $filterRecordId = $filterValue;

		      if (!$filterRecordId)
		      {
		        throw new dmException(sprintf('No filter record found for %s %d', $filterModule, $filterValue));
		      }
		    }
		    else
		    {
		      $filterRecordId = $this->getDmContext()->getPage()->record->getAncestorRecordId($filterModule->getModel());

		      if (!$filterRecordId)
		      {
		        throw new dmException(sprintf('Can not determine auto filter %s for page %s', $filterModule, $page));
		      }
		    }

		    $query->whereAncestorId($filterModule->getModel(), $filterRecordId, $this->getDmModule()->getModel());
      }
		}

		return $query;
	}

	protected function getDmModule()
	{
		if (is_null($this->dmModule))
		{
			$this->dmModule = dmModuleManager::getModule(preg_replace('|^(.+)Components$|', '$1', get_class($this)));
		}

		return $this->dmModule;
	}
	
	protected function getTable()
	{
		return $this->getDmModule()->getTable();
	}

}