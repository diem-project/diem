<?php

class dmDoctrinePager extends sfDoctrinePager
{
  protected
  $resultsCache;

  /**
   * @see sfPager
   */
  public function setMaxPerPage($maxPerPage)
  {
    parent::setMaxPerPage($maxPerPage);

    return $this;
  }

  /**
   * @see sfPager
   */
  public function setQuery($query)
  {
    parent::setQuery($query);

    return $this;
  }

  /**
   * @see sfPager
   */
  public function setPage($page)
  {
    parent::setPage($page);

    return $this;
  }

  /**
   * @see sfPager
   */
  public function init()
  {
    parent::init();

    return $this;
  }
  
  /**
   * Get all the results for the pager instance
   *
   * @param mixed $hydrationMode A hydration mode identifier
   *
   * @return Doctrine_Collection|array
   */
  public function getResults($hydrationMode = null)
  {
    if (null === $this->resultsCache)
    {
      $this->resultsCache = parent::getResults($hydrationMode)->getData();
    }
    
    return $this->resultsCache;
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