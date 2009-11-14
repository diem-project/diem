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
      $this->cache = parent::getResults($hydrationMode)->getData();
    }
    
    return $this->cache;
  }
  
  public function serialize()
  {
    $vars = get_object_vars($this);
    unset($vars['query'], $vars['cache']);
    return serialize($vars);
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