<?php

class dmFrontModuleComponents extends myFrontBaseComponents
{

  protected
  $dmModule;
  
  /*
   * Add required stuff to the record query
   * @param string $rootAlias        The root alias for this query
   * @return myDoctrineQuery
   */
  protected function getShowQuery($rootAlias = 'root')
  {
    $query = $this->getTable()->createQuery($rootAlias);

    if ($this->recordId)
    {
      $query->addWhere($query->getRootAlias().'.id = ?', $this->recordId)->fetchRecord();
    }
    elseif ($this->getPage()->getDmModule()->hasModel())
    {
      $query->whereDescendantId($this->getPage()->getDmModule()->getModel(), $this->getPage()->get('record_id'), $this->getDmModule()->getModel());
    }

    return $query;
  }

  /*
   * @param myDoctrineQuery $query        The query used to fetch the record
   * @return myDoctrineRecord $record
   */
  protected function getRecord(dmDoctrineQuery $query)
  {
    $record = $query->fetchOne();

    if (!$record instanceof dmDoctrineRecord)
    {
      throw new dmException(sprintf('No record found for %s %d', $this->getDmModule(), $this->recordId));
    }

    return $record;
  }

  /*
   * Add required stuff to the list query
   * @param string $rootAlias        The root alias for this query
   * @return myDoctrineQuery
   */
  protected function getListQuery($rootAlias = 'root')
  {
    $query = $this->getTable()->createQuery($rootAlias);

    /*
     * Restrict to active records
     */
    $query->whereIsActive(true, $this->getDmModule()->getModel());
    
    /*
     * Apply order
     */
    if(!empty($this->orderType))
    {
      if ($this->orderType == 'rand')
      {
        $query->addOrderBy('RAND()');
      }
      else
      {
        $query->addOrderBy($this->orderField.' '.$this->orderType);
      }
    }

    /*
     * Apply filters
     */
    if(!empty($this->filters))
    {
      foreach($this->filters as $filterKey => $filterValue)
      {
        if (($filterModule = $this->getDmModule()->getAncestor($filterKey)) || ($filterModule = $this->getDmModule()->getAssociation($filterKey)))
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
            $filterRecordId = $this->getPage()->getRecord()->getAncestorRecordId($filterModule->getModel());
  
            if (!$filterRecordId)
            {
              throw new dmException(sprintf('Can not determine auto filter %s for page %s', $filterModule, $page));
            }
          }
  
          $query->whereAncestorId($filterModule->getModel(), $filterRecordId, $this->getDmModule()->getModel());
        }
        else
        {
          throw new dmException(sprintf('Can not process filter %s on module %s', $filterKey, $this->getDmModule()));
        }
      }
    }

    return $query;
  }

  /*
   * @param myDoctrineQuery $query        The query passed to pager
   * @return myDoctrinePager $pager
   */
  protected function getPager(myDoctrineQuery $query, $page = null)
  {
    $pager = $this->context->getServiceContainer()->getDoctrinePager($this->getDmModule()->getModel(), $this->maxPerPage);

    $pager->setQuery($query);

    $pager->setPage(null === $page ? $this->request->getParameter('page', 1) : $page);

    $pager->configureNavigation(array(
      'top'     => $this->navTop,
      'bottom'  => $this->navBottom
    ));

    $pager->init();
    
    try
    {
      $this->preloadPages($pager->getResults());
    }
    catch(Exception $e)
    {
      $this->getLogger()->err(sprintf('Can not prepare page cache for module %s : %s', $this->getDmModule()->getKey(), $e->getMessage()));
      
      if (sfConfig::get('dm_debug'))
      {
        throw $e;
      }
    }
    
    return $pager;
  }
  
  /*
   * Preload all pages related to records
   */
  protected function preloadPages($records)
  {
    if ($records instanceof Doctrine_Collection)
    {
      $records = $records->getData();
    }
        
    if (!empty($records))
    {
      if (($module = dmArray::first($records)->getDmModule()) && $module->hasPage())
      {
        $ids = array();
        foreach($records as $record)
        {
          $ids[] = $record->get('id');
        }
        
        dmDb::table('DmPage')->prepareRecordPageCache($module->getKey(), array_unique($ids));
      }
    }
  }

  /*
   * @return dmModule the current module for this component
   */
  protected function getDmModule()
  {
    if (null === $this->dmModule)
    {
      $this->dmModule = $this->context->getModuleManager()->getModule(preg_replace('|^(.+)Components$|', '$1', get_class($this)));
    }

    return $this->dmModule;
  }

  /*
   * @return myDoctrineTable the table of the current module for this component
   */
  protected function getTable()
  {
    return $this->getDmModule()->getTable();
  }

}