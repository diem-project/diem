<?php
/**
 * Main actions
 */
class mainActions extends myFrontModuleActions
{

  public function executeLoginFormWidget(dmWebRequest $request)
  {
    $user = $this->getUser();

    // by assigning the form to $this->forms,
    // we allow the loginForm component to access it
    $form = $this->forms['dmFormSignin'];

    if ($request->isMethod('post'))
    {
      if ($form->bindAndValid($request))
      {
        $this->getUser()->signin($form->getValue('user'), $form->getValue('remember', false));

        return $this->redirect($request->getReferer());
      }
    }
  }


}
