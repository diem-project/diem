<?php

class BasedmUserActions extends myFrontModuleActions
{

  /**
   * Handle dmUser/signin form validation and authenticates the user
   */
  public function executeSigninWidget(dmWebRequest $request)
  {
    $form = new DmSigninFrontForm();

    $user = $this->getUser();

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

    $this->forms['DmSigninFront'] = $form;
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
    $form = new DmUserForm();

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

    $this->forms['DmUser'] = $form;
  }

  /**
   * Override this method to redirect the user to some page
   * just after he(she) successfully registered.
   */
  protected function redirectRegisteredUser(dmWebRequest $request)
  {
    $this->redirect($request->getReferer());
  }

  /**
   * Handle dmUser/forgotPassword form validation and sends an email with a new password
   */
  public function executeForgotPasswordWidget(dmWebRequest $request)
  {
    $form = new DmForgotPasswordForm();

    $user = $this->getUser();

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
        $user = $form->getUserByEmail($form->getValue('email'));
        $newPassword = dmString::random(7);
        $user->password = $newPassword;
        $user->save();

        $this->getService('mail')
        ->setTemplate('dm_user_forgot_password')
        ->addValues(array(
          'username'      => $user->username,
          'email'         => $user->email,
          'new_password'  => $newPassword
        ))
        ->send();

        $this->getUser()->setFlash('dm_new_password_sent', $user->email);

        $this->redirectBack();
      }
    }

    $this->forms['DmForgotPassword'] = $form;
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
