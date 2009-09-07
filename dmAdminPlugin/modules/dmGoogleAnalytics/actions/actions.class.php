<?php

class dmGoogleAnalyticsActions extends dmAdminBaseActions
{
  
  public function executeIndex(dmWebRequest $request)
  {
    $this->form = new dmGoogleAnalyticsForm;
  }
  
}