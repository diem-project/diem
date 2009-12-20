<?php
/**
 * Comment components
 * 
 * No redirection nor database manipulation ( insert, update, delete ) here
 */
class commentComponents extends myFrontModuleComponents
{

  public function executeListByPost()
  {
    $query = $this->getListQuery();
    $this->commentPager = $this->getPager($query);
  }

  public function executeForm()
  {
    $this->form = $this->forms['Comment'];
  }


}
