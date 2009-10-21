<?php

class dmDoctrinePager extends sfDoctrinePager
{
  protected
  $cache;
  
  /**
   * Get all the results for the pager instance
   *
   * @param mixed $hydrationMode A hydration mode identifier
   *
   * @return Doctrine_Collection|array
   */
  public function getResults($hydrationMode = null)
  {
    if (null === $this->cache)
    {
      $this->cache = parent::getResults($hydrationMode);
    }
    
    return $this->cache;
  }
  
  /**
   * @see sfPager
   */
  public function count()
  {
    return count($this->getResults());
  }
 
//  public function getCollection($hydrationMode = Doctrine::HYDRATE_RECORD)
//  {
//    if(null !== $this->collection)
//    {
//      return $this->collection;
//    }
//    
//    return $this->collection = $this->getQuery()->execute(array(), $hydrationMode);
//  }

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