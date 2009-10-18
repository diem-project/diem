<?php

class dmGoogleAnalyticsActions extends dmAdminBaseActions
{
  
  public function executeIndex(dmWebRequest $request)
  {
    $setting = dmDb::table('DmSetting')->fetchOneByName('ga_key');
    
    if($this->getUser()->can($setting->get('credentials')))
    {
      $this->form = new dmConfigForm;
      $this->form->addSetting($setting);
      
      if ($request->isMethod('post') && $this->form->bindAndValid($request))
      {
        $this->form->save();
        return $this->redirect('@dm_google_analytics');
      }
    }
  }
  
}