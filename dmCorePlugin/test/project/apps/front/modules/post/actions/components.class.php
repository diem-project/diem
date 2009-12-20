<?php
/**
 * Post components
 * 
 * No redirection nor database manipulation ( insert, update, delete ) here
 */
class postComponents extends myFrontModuleComponents
{

  public function executeList()
  {
    $query = $this->getListQuery();
    $this->postPager = $this->getPager($query);
  }

  public function executeShow()
  {
    $query = $this->getShowQuery();
    $this->post = $this->getRecord($query);
  }


}
