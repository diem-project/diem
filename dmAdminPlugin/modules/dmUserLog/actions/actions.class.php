<?php

class dmConfigPanelActions extends dmAdminBaseActions
{

  public function executeIndex(dmWebRequest $request)
  {
    $this->form = new dmConfigForm;

    $this->settings = dmDb::table('DmSetting')->fetchGrouped();

    $this->groups = array_keys($this->settings);

    foreach($this->settings as $group => $settings)
    {
      foreach($settings as $setting)
      {
        if($this->getUser()->can($setting->get('credentials')))
        {
          $this->form->addSetting($setting);

          if(!in_array($setting->get('group_name'), $this->groups))
          {
            $this->groups[] = $setting->get('group_name');
          }
        }
      }
    }
  }

}