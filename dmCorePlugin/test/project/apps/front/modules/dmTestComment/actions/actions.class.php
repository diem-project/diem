<?php
/**
 * Dm test comment actions
 */
class dmTestCommentActions extends myFrontModuleActions
{

  public function executeFormWidget(dmWebRequest $request)
  {
    $form = new DmTestCommentForm();
        
    if ($request->isMethod('post') && $form->bindAndValid($request))
    {
      $form->save();
      $this->redirectBack();
    }

    $this->forms['DmTestComment'] = $form;
  }


}
