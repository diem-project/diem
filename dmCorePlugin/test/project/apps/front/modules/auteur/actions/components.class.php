<?php
/**
 * Auteur components
 * 
 * No redirection nor database manipulation ( insert, update, delete ) here
 */
class auteurComponents extends myFrontModuleComponents
{

  public function executeList()
  {
    $query = $this->getListQuery();
    $this->auteurPager = $this->getPager($query);
  }

  public function executeShow()
  {
    $query = $this->getShowQuery();
    $this->auteur = $this->getRecord($query);
  }


}
