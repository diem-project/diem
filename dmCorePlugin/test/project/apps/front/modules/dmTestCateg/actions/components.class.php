<?php
/**
 * Dm test categ components
 * 
 * No redirection nor database manipulation ( insert, update, delete ) here
 * 
 */
class dmTestCategComponents extends myFrontModuleComponents
{

  public function executeList()
  {
    $query = $this->getListQuery();
    $this->dmTestCategPager = $this->getPager($query);
  }

  public function executeShow()
  {
    $query = $this->getShowQuery();
    $this->dmTestCateg = $this->getRecord($query);
  }

  public function executeListByDomain()
  {
    $query = $this->getListQuery();
    $this->dmTestCategPager = $this->getPager($query);
  }


}
