<?php
/**
 * Dm test post components
 * 
 * No redirection nor database manipulation ( insert, update, delete ) here
 * 
 * 
 */
class dmTestPostComponents extends myFrontModuleComponents
{

  public function executeList()
  {
    $query = $this->getListQuery();
    $this->dmTestPostPager = $this->getPager($query);
  }

  public function executeShow()
  {
    $query = $this->getShowQuery();
    $this->dmTestPost = $this->getRecord($query);
  }

  public function executeListByCateg()
  {
    $query = $this->getListQuery();
    $this->dmTestPostPager = $this->getPager($query);
  }

  public function executeListByTag()
  {
    $query = $this->getListQuery();
    $this->dmTestPostPager = $this->getPager($query);
  }

  public function executeListByDomain()
  {
    $query = $this->getListQuery();
    $this->dmTestPostPager = $this->getPager($query);
  }


}
