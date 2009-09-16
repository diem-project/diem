<?php

require_once(dm::getDir().'/dmGuardPlugin/modules/sfGuardAuth/lib/BasesfGuardAuthActions.class.php');

class dmAuthActions extends BasesfGuardAuthActions
{

  public function executeSignin($request)
  {
  	if ($request->isXmlHttpRequest())
  	{
  		return $this->ajaxLogin($request);
  	}
  	
  	if (sfConfig::get('sf_environment') != 'test' && !$this->getUser()->getBrowser()->isModern())
  	{
  		return $this->forward('dmAuth', 'badBrowser');
  	}
  	
  	$this->helper = $this->dmContext->getService('auth_layout_helper');

    $this->setLayout(dmOs::join(sfConfig::get('dm_core_dir'), 'modules/dmAuth/templates/layout'));

    return parent::executeSignin($request);
  }
  
  public function executeBadBrowser()
  {
  	
  }


}