<?php
/**
 * Dm test tag components
 * 
 * No redirection nor database manipulation ( insert, update, delete ) here
 * 
 */
class dmTestTagComponents extends myFrontModuleComponents
{

  public function executeList()
  {
    $query = $this->getListQuery();
    $this->dmTestTagPager = $this->getPager($query);
  }

  public function executeShow()
  {
    $query = $this->getShowQuery();
    $this->dmTestTag = $this->getRecord($query);
  }

  public function executeListByPost()
  {
    $query = $this->getListQuery();
    $this->dmTestTagPager = $this->getPager($query);
  }


}
