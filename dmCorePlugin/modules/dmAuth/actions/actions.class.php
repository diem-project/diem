<?php

require_once(dm::getDir().'/dmGuardPlugin/modules/sfGuardAuth/lib/BasesfGuardAuthActions.class.php');

class dmAuthActions extends BasesfGuardAuthActions
{

  public function executeSignin($request)
  {
    if ($request->isXmlHttpRequest())
    {
      $this->getResponse()->setHeaderOnly(true);
      $this->getResponse()->setStatusCode(401);

      return sfView::NONE;
    }
    
    if (sfConfig::get('sf_environment') != 'test' && !$this->getUser()->getBrowser()->isModern())
    {
      return $this->forward('dmAuth', 'badBrowser');
    }
    
    $this->helper = $this->context->get('auth_layout_helper');

    $this->setLayout(dmOs::join(sfConfig::get('dm_core_dir'), 'modules/dmAuth/templates/layout'));

    return parent::executeSignin($request);
  }
  
  public function executeBadBrowser()
  {
    
  }


}