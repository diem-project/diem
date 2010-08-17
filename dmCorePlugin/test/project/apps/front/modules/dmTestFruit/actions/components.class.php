<?php
/**
 * Dm test fruit components
 * 
 * No redirection nor database manipulation ( insert, update, delete ) here
 */
class dmTestFruitComponents extends myFrontModuleComponents
{

  public function executeList()
  {
    $query = $this->getListQuery();
    $this->dmTestFruitPager = $this->getPager($query);
  }

  public function executeShow()
  {
    $query = $this->getShowQuery();
    $this->dmTestFruit = $this->getRecord($query);
  }


}
