<?php

class dmFrontBaseActions extends dmBaseActions
{
	
	public function getPage()
	{
		return $this->getDmContext()->getPage();
	}
	

  /**
   * Indicates that this action requires security.
   *
   * @return bool true, if this action requires security, otherwise false.
   */
  public function isSecure()
  {
  	if (!dmConfig::get('site_active'))
  	{
  		return true;
  	}

  	return parent::isSecure();
  }

  /**
   * Gets credentials the user must have to access this action.
   *
   * @return mixed An array or a string describing the credentials the user must have to access this action
   */
  public function getCredential()
  {
  	$credentials = parent::getCredential();

    if (!dmConfig::get('site_active'))
    {
  	  $credentials[] = 'view_site';
    }

    return $credentials;
  }
  
  protected function redirectBack()
  {
    $refererUrl = $this->request->getReferer();

    if (!$refererUrl || $refererUrl === $this->request->getUri())
    {
      if ($page = $this->getDmContext()->getPage())
      {
        $refererUrl = dmFrontLinkTag::build($page)->getAbsoluteHref();
      }
      else
      {
        $refererUrl = dmFrontLinkTag::build()->getAbsoluteHref();
      }
    }
    
    return $this->redirect($refererUrl);
  }
}