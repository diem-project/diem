<?php

class BasedmTagComponents extends myFrontModuleComponents
{

  public function executeList()
  {
    $query = $this->getListQuery();

    $this->dmTagPager = $this->getPager($query);
  }

  public function executePopular()
  {
    $this->dmTags = dmDb::table('DmTag')->getPopularTags(array(), 100);
  }

  public function executeShow()
  {
    $query = $this->getShowQuery();

    $this->dmTag = $this->getRecord($query);
  }

}