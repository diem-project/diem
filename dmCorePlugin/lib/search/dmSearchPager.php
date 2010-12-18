<?php

/**
 * The pager to separate results into more manageable segments.
 *
 * Page starts counting at 1.
 *
 */
class dmSearchPager extends sfPager
{
  protected
  $hits;

  /**
   * Constructor.
   *
   * @param string  $hits       The search results
   * @param integer $maxPerPage Number of records to display per page
   */
  public function __construct($hits, $maxPerPage = 10)
  {
    $this->setHits($hits);
    $this->setMaxPerPage($maxPerPage);
    $this->parameterHolder = new sfParameterHolder();
  }

  /**
   * Sets the current hits.
   *
   * @param array $hits
   */
  public function setHits($hits)
  {
    $this->hits = $hits;
  }
  
  // function to be called after parameters have been set
  public function init()
  {
    $this->setNbResults(count($this->hits));
    
    if ($this->getPage() == 0 || $this->getMaxPerPage() == 0 || $this->getNbResults() == 0)
    {
      $this->setLastPage(0);
    }
    else
    {
      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
    }
  }

  // main method: returns an array of result on the given page
  public function getResults()
  {
    $offset = ($this->getPage() - 1) * $this->getMaxPerPage();
    
    $hits = array_slice($this->hits, $offset, $this->getMaxPerPage());
    
    $hits = $this->preloadHitPages($hits);
    
    return $hits;
  }
  
  protected function preloadHitPages(array $hits)
  {
    $pageIds = array();
    foreach($hits as $hit)
    {
      $pageIds[] = $hit->getPageId();
    }
    
    $pages = dmDb::query('DmPage p INDEXBY p.id')
    ->whereIn('p.id', $pageIds)
    ->withI18n()
    ->fetchRecords();
    
    foreach($hits as $index => $hit)
    {
      if(empty($pages[$hit->getPageId()]))
      {
        unset($hits[$index]);
      }
      else
      {
        $hit->setPage($pages[$hit->getPageId()]);
      }
    }
    unset($pages);
    
    return $hits;
  }

  // used internally by getCurrent()
  protected function retrieveObject($offset)
  {
    return $this->hits[$offset];
  }
}