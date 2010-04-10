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

  public function executeForgotPassword(sfWebRequest $request)
  {
    if(isset($this->forms['DmForgotPasswordStep1']))
    {
      $this->step = 1;
      $this->form = $this->forms['DmForgotPasswordStep1'];
    }
    else
    {
      $this->step = 2;
      $this->form = $this->forms['DmForgotPasswordStep2'];
    }
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