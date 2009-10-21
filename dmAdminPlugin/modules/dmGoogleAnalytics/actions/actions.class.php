<?php

class dmGoogleAnalyticsActions extends dmAdminBaseActions
{
  
  public function executeIndex(dmWebRequest $request)
  {
    $settings = dmDb::query('DmSetting s')
    ->whereIn('s.name', array('ga_key', 'ga_email', 'ga_password'))
    ->fetchRecords()->getData();
    
    foreach($settings as $index => $setting)
    {
      if (!$this->getUser()->can($setting->get('credentials')))
      {
        unset($settings[$index]);
      }
    }
    
    if(!empty($settings))
    {
      $this->form = new dmConfigForm;
      $this->form->addSettings($settings);
      
      if ($request->isMethod('post') && $this->form->bindAndValid($request))
      {
        $this->form->save();
        return $this->redirect('@dm_google_analytics');
      }
    }
  }
  
}