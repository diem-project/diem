<?php

class BasedmUserActions extends myFrontModuleActions
{

  /**
   * Handle dmUser/signin form validation and authenticates the user
   */
  public function executeSigninWidget(dmWebRequest $request)
  {
    $form = $this->forms['DmSigninFront'];

    if ($request->isMethod('post') && $request->hasParameter($form->getName()))
    {
      if ($form->bindAndValid($request))
      {
        $this->getUser()->signin($form->getValue('user'), $form->getValue('remember'));

        $this->redirectSignedInUser();
      }
    }
  }

  /**
   * Override this method to redirect the user to some page
   * just after he(she) successfully signed in.
   */
  protected function redirectSignedInUser()
  {
    $this->redirect($request->getReferer());
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

        $this->redirectRegisteredUser();
      }
    }
  }

  /**
   * Override this method to redirect the user to some page
   * just after he(she) successfully registered.
   */
  protected function redirectRegisteredUser()
  {
    $this->redirect($request->getReferer());
  }

  public function executeSignin(dmWebRequest $request)
  {
    $request->setParameter('dm_page', dmDb::table('DmPage')->fetchSignin());

    $this->getResponse()->setStatusCode(401);

    return $this->forward('dmFront', 'page');
  }

  public function executeSecure(dmWebRequest $request)
  {
    $request->setParameter('dm_page', dmDb::table('DmPage')->fetchSignin());
    
    $this->getResponse()->setStatusCode(403);

    return $this->forward('dmFront', 'page');
  }

  public function executeSignout($request)
  {
    $this->getUser()->signOut();

    $signoutUrl = sfConfig::get('dm_security_success_signout_url', $request->getReferer());

    $this->redirect('' != $signoutUrl ? $signoutUrl : '@homepage');
  }
}