<?php
/**
 * Author components
 * 
 * No redirection nor database manipulation ( insert, update, delete ) here
 */
class dmUserComponents extends myFrontModuleComponents
{

  public function executeList()
  {
    $query = $this->getListQuery();
    $this->dmUserPager = $this->getPager($query);
  }

  public function executeShow()
  {
    $query = $this->getShowQuery();
    $this->dmUser = $this->getRecord($query);
  }


}
