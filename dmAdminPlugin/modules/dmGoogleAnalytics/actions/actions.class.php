<?php

class dmGoogleAnalyticsActions extends dmAdminBaseActions
{
  
  public function executeIndex(dmWebRequest $request)
  {
    if($this->getUser()->can('google_analytics'))
    {
      $this->form = new dmGoogleAnalyticsForm();
      $this->form->setGapi($this->getService('gapi'));
      
      if ($request->isMethod('post'))
      {
        dmConfig::set('ga_key', dmArray::get($request->getParameter($this->form->getName()), 'key'));
        
        if($this->form->bindAndValid($request))
        {
          $this->form->save();
          return $this->redirect('@dm_google_analytics');
        }
      }
    }

    $this->gapiConnected = false;
    
    if(dmConfig::get('ga_token'))
    {
      try
      {
        $this->getService('gapi')->authenticate(null, null, dmConfig::get('ga_token'));
        $this->gapiConnected = true;
      }
      catch(dmGapiException $e)
      {
        // bad token
      }
    }
  }
  
}