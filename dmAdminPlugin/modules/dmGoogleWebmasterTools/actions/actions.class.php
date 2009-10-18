<?php

class dmGoogleWebmasterToolsActions extends dmAdminBaseActions
{

  public function executeIndex(dmWebRequest $request)
  {
    $setting = dmDb::table('DmSetting')->fetchOneByName('gwt_key');
    
    if($this->getUser()->can($setting->get('credentials')))
    {
      $this->form = new dmConfigForm;
      $this->form->addSetting($setting);
      
      if ($request->isMethod('post') && $this->form->bindAndValid($request))
      {
        $this->form->save();
        return $this->redirect('@dm_google_webmaster_tools');
      }
    }
  }
  
}
