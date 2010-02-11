<?php
/**
 * Dm test domain components
 * 
 * No redirection nor database manipulation ( insert, update, delete ) here
 * 
 */
class dmTestDomainComponents extends myFrontModuleComponents
{

  public function executeList()
  {
    $query = $this->getListQuery();
    $this->dmTestDomainPager = $this->getPager($query);
  }

  public function executeShow()
  {
    $query = $this->getShowQuery();
    $this->dmTestDomain = $this->getRecord($query);
  }

  public function executeListByTag()
  {
    $query = $this->getListQuery();
    
    $this->dmTestDomainPager = $this->getPager($query);
  }


}
