<?php

class dmConfigPanelActions extends dmAdminBaseActions
{

  public function executeIndex(dmWebRequest $request)
  {
    $this->form = new dmConfigForm;

    $this->settings = dmDb::table('DmSetting')->fetchGrouped();

    $this->groups = array();

    foreach($this->settings as $group => $settings)
    {
      if ('internal' == $group)
      {
        continue;
      }
      
      foreach($settings as $index => $setting)
      {
        if($this->getUser()->can($setting->get('credentials')))
        {
          $this->form->addSetting($setting);

          if(!in_array($setting->get('group_name'), $this->groups))
          {
            $this->groups[] = $setting->get('group_name');
          }
        }
        else
        {
          unset($this->settings[$group][$index]);
        }
      }
      if(empty($this->settings[$group]))
      {
        unset($this->settings[$group]);
      }
    }

    $this->getDispatcher()->notify(new sfEvent($this->form, 'form.post_configure'));

    if($request->isMethod('post'))
    {
      if ($this->form->bindAndValid($request))
      {
        $formValues = $this->form->getValues();
        foreach($this->settings as $group => $settings)
        {
          foreach($settings as $index => $setting)
          {
            $settingName = $setting->get('name');
            if (isset($formValues[$settingName]) && $formValues[$settingName] != $setting->value)
            {
              dmConfig::set($settingName, $formValues[$settingName]);
            }
          }
        }
        
        $this->getUser()->logInfo('Your modifications have been saved', true);
        
        return $this->redirect('@dm_config_panel');
      }
      else
      {
        $this->getUser()->logAlert('The item has not been saved due to some errors.', true);
      }
    }
  }
}