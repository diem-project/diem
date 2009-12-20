<?php
/**
 * Comment actions
 */
class commentActions extends myFrontModuleActions
{

  public function executeFormWidget(dmWebRequest $request)
  {
    $form = $this->forms['Comment'];
        
    if ($request->isMethod('post') && $form->bindAndValid($request))
    {
      $form->save();
      $this->redirectBack();
    }
  }


}
