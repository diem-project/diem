<?php
/**
 * Dm test comment components
 * 
 * No redirection nor database manipulation ( insert, update, delete ) here
 * 
 */
class dmTestCommentComponents extends myFrontModuleComponents
{

  public function executeListByPost()
  {
    $query = $this->getListQuery();
    $this->dmTestCommentPager = $this->getPager($query);
  }

  public function executeForm()
  {
    $this->form = $this->forms['DmTestComment'];
  }

  public function executeListByDomain()
  {
    $query = $this->getListQuery();
    $this->dmTestCommentPager = $this->getPager($query);
  }

  public function executeListByCateg()
  {
    $query = $this->getListQuery();
    $this->dmTestCommentPager = $this->getPager($query);
  }


}
