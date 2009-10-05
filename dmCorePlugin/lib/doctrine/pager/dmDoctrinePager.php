<?php

class dmDoctrinePager extends sfDoctrinePager
{
  
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
    
    return $this->results = $this->getQuery()->execute(array(), $hydrationMode)->getData();
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