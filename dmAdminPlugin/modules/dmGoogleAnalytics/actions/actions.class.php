<?php

class dmGoogleAnalyticsActions extends dmAdminBaseActions
{
  
  public function executeIndex(dmWebRequest $request)
  {
    $this->site = $this->getDmContext()->getSite();
    
    $this->form = new dmGoogleAnalyticsForm($this->site);
  }
  
}