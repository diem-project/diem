<?php

class BasedmUserAdminActions extends autodmUserAdminActions
{

  public function executeSignin(dmWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated())
    {
      return $this->redirect('@homepage');
    }

    $this->setLayout(realpath(dirname(__FILE__).'/..').'/templates/layout');

    if($request->getParameter('skip_browser_detection'))
    {
      $this->getService('browser_check')->markAsChecked();
    }
    elseif(!$this->getService('browser_check')->check())
    {
      return 'Browser';
    }

    $this->form = new DmSigninAdminForm();

    if ($request->isMethod('post'))
    {
      $this->form->bindRequest($request);

      if ($this->form->isValid())
      {
        $this->getUser()->signin($this->form->getValue('user'), $this->form->getValue('remember'));

        if ($this->getUser()->can('admin'))
        {
          $redirectUrl = $this->getUser()->getReferer($request->getReferer());
          
          $this->redirect($redirectUrl ? $redirectUrl : '@homepage');
        }
        else
        {
          try
          {
            $this->redirect($this->getService('script_name_resolver')->get('front'));
          }
          catch(dmException $e)
          {
            // user can't go in admin, and front script_name can't be found.
            $this->redirect('@homepage');
          }
        }
      }
    }
    else
    {
      if ($request->isXmlHttpRequest())
      {
        $this->getResponse()->setHeaderOnly(true);
        $this->getResponse()->setStatusCode(401);

        return sfView::NONE;
      }

      // if we have been forwarded, then the referer is the current URL
      // if not, this is the referer of the current request
      $this->getUser()->setReferer($this->getContext()->getActionStack()->getSize() > 1 ? $request->getUri() : $request->getReferer());

      $module = sfConfig::get('sf_login_module');
      if ($this->getModuleName() != $module)
      {
        return $this->redirect($module.'/'.sfConfig::get('sf_login_action'));
      }

      $this->getResponse()->setStatusCode(401);
    }
  }

  public function executeSignout($request)
  {
    $this->getUser()->signOut();

    $signoutUrl = sfConfig::get('dm_security_success_signout_url', $request->getReferer());

    $this->redirect('' != $signoutUrl ? $signoutUrl : '@homepage');
  }

  public function executeSecure()
  {
    $this->setLayout(realpath(dirname(__FILE__).'/..').'/templates/layout');

    $this->getResponse()->setStatusCode(403);
  }

  public function executePassword()
  {
    throw new sfException('This method is not yet implemented.');
  }
  
  public function validateEdit()
  {
    if ($this->getRequest()->isMethod('post') && !$this->getRequestParameter('id'))
    {
      if ($this->getRequestParameter('dm_user[password]') == '')
      {
        $this->getRequest()->setError('dm_user{password}', $this->getContext()->getI18N()->__('Password is mandatory'));

        return false;
      }
    }

    return true;
  }

  public function executeDelete(sfWebRequest $request)
  {
    try
    {
      return parent::executeDelete($request);
    }
    catch(dmRecordException $e)
    {
      $this->getUser()->logError($e->getMessage());
      $this->redirectBack();
    }
  }
}
