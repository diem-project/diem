<?php

class dmDoctrinePager extends sfDoctrinePager
{
  protected
  $collection;
  
  /**
   * Get all the results for the pager instance
   *
   * @param integer $hydrationMode Doctrine::HYDRATE_* constants
   *
   * @return Doctrine_Collection|array
   */
  public function getResults($hydrationMode = Doctrine::HYDRATE_RECORD)
  {
    if(null !== $this->results)
    {
      return $this->results;
    }
    
    return $this->results = $this->getCollection($hydrationMode)->getData();
  }
  
  public function getCollection($hydrationMode = Doctrine::HYDRATE_RECORD)
  {
    if(null !== $this->collection)
    {
      return $this->collection;
    }
    
    return $this->collection = $this->getQuery()->execute(array(), $hydrationMode);
  }

  public function getCountQuery()
  {
    $selectQuery = $this->getQuery();
    
    if (count($selectQuery->getDqlPart('where')))
    {
      $query = clone $selectQuery;
      return $query->offset(0)->limit(0);
    }
    else
    {
      return dmDb::table($this->getClass())->createQuery();
    }
  }
}