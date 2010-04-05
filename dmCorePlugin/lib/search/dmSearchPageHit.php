<?php

class dmSearchPageHit
{
  protected
  $pageId,
  $pageContent,
  $score,
  $page;
  
  public function __construct($score, $pageId, $pageContent)
  {
    $this->score = $score;
    $this->pageId = $pageId;
    $this->pageContent = $pageContent;
  }
  
  public function getScore()
  {
    return $this->score;
  }
  
  public function getPage()
  {
    if(null === $this->page)
    {
      $this->page = dmDb::table('DmPage')->findOneByIdWithI18n($this->pageId);
    }
    
    return $this->page;
  }

  public function getPageId()
  {
    return $this->pageId;
  }

  public function getPageContent()
  {
    return $this->pageContent;
  }
  
  public function setPage(DmPage $page)
  {
    $this->page = $page;
  }
}