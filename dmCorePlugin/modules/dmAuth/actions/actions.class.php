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

    $this->setLayout(dmOs::join(sfConfig::get('dm_core_dir'), 'modules/dmAuth/templates/layout'));

    $this->site = dmContext::getInstance()->getSite();

    return parent::executeSignin($request);
  }


  protected function ajaxLogin(sfWebRequest $request)
  {
    $class = sfConfig::get('app_sf_guard_plugin_signin_form', 'sfGuardFormSignin');
    $this->form = new $class();

    $this->form->bind(array(
      'username' => $request->getParameter('username'),
      'password' => base64_decode($request->getParameter('password'))
    ));

    if ($this->form->isValid())
    {
      return $this->renderText('ok');
    }
    else
    {
    	return $this->renderText('ko');
    }
  }

}