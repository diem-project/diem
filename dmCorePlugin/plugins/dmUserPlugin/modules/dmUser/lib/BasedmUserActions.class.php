<?php

class BasedmUserActions extends myFrontModuleActions
{

  /**
   * Handle dmUser/signin form validation and authenticates the user
   */
  public function executeSigninWidget(dmWebRequest $request)
  {
    $form = $this->forms['DmSigninFront'];

    $user = $this->getUser();
    
    if ($user->isAuthenticated() && $this->getPage() != dmDb::table('DmPage')->getTree()->fetchRoot())
    {
      return $this->redirect('@homepage');
    }

    if ($request->isMethod('post') && $request->hasParameter($form->getName()))
    {
      if ($form->bindAndValid($request))
      {
        $user->signin($form->getValue('user'), $form->getValue('remember'));

        $this->redirectSignedInUser($request);
      }
    }
    else
    {
      if ($request->isXmlHttpRequest())
      {
        $this->getResponse()->setStatusCode(401);
        $this->getResponse()->setHeaderOnly(true);
        return sfView::NONE;
      }

      // if we have been forwarded, then the referer is the current URL
      // if not, this is the referer of the current request
      $user->setReferer($this->getContext()->getActionStack()->getSize() > 1 ? $request->getUri() : $request->getReferer());
    }
  }

  /**
   * Override this method to redirect the user to some page
   * just after he(she) successfully signed in.
   */
  protected function redirectSignedInUser(dmWebRequest $request)
  {
    $redirectUrl = $this->getUser()->getReferer($request->getReferer());

    $this->redirect('' != $redirectUrl ? $redirectUrl : '@homepage');
  }

  /**
   * Handle dmUser/form form validation and creates the user account, then authenticates the user
   */
  public function executeFormWidget(dmWebRequest $request)
  {
    $form = $this->forms['DmUser'];

    if ($request->isMethod('post') && $request->hasParameter($form->getName()))
    {
      $data = $request->getParameter($form->getName());
      
      if($form->isCaptchaEnabled())
      {
        $data = array_merge($data, array('captcha' => array(
          'recaptcha_challenge_field' => $request->getParameter('recaptcha_challenge_field'),
          'recaptcha_response_field'  => $request->getParameter('recaptcha_response_field'),
        )));
      }

      $form->bind($data, $request->getFiles($form->getName()));
      
      if ($form->isValid())
      {
        $user = $form->save();

        $this->getUser()->signin($user);

        $this->redirectRegisteredUser($request);
      }
    }
  }

  /**
   * Override this method to redirect the user to some page
   * just after he(she) successfully registered.
   */
  protected function redirectRegisteredUser(dmWebRequest $request)
  {
    $this->redirect($request->getReferer());
  }

  public function executeSignin(dmWebRequest $request)
  {
    $request->setParameter('dm_page', dmDb::table('DmPage')->fetchSignin());

    $this->getResponse()->setStatusCode(401);

    $this->forward('dmFront', 'page');
  }

  public function executeSecure(dmWebRequest $request)
  {
    $request->setParameter('dm_page', dmDb::table('DmPage')->fetchSignin());

    $this->getResponse()->setStatusCode(403);

    $this->forward('dmFront', 'page');
  }

  public function executeSignout($request)
  {
    $this->getUser()->signOut();

    $signoutUrl = sfConfig::get('dm_security_success_signout_url', $request->getReferer());

    $this->redirect('' != $signoutUrl ? $signoutUrl : '@homepage');
  }
}