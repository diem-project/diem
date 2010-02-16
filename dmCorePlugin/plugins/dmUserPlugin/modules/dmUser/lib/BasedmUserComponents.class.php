<?php

class BasedmUserComponents extends myFrontModuleComponents
{

  public function executeSignin()
  {
    $this->form = $this->forms['DmSigninFront'];
  }

  public function executeForm()
  {
    $this->form = $this->forms['DmUser'];
  }

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