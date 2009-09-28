<?php

class dmDoctrinePager extends sfDoctrinePager
{

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