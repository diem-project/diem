<?php

class dmGuardUserAdminForm extends sfGuardUserAdminForm
{
  public function configure()
  {
    parent::configure();
//    dmDebug::kill($this->getObject()->Profile);
    $this->embedForm('Profile', $this->getProfileForm($this->getObject()->Profile));
  }
  
  protected function getProfileForm(DmProfile $profile)
  {
    $profileForm = new DmProfileForm($profile);
    unset($profileForm['created_at'], $profileForm['updated_at'], $profileForm['user_id']);
    
    return $profileForm;
  }
}