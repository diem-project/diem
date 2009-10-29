<?php

class dmGuardUserAdminForm extends sfGuardUserAdminForm
{
  public function configure()
  {
    parent::configure();

    $this->unsetAutoFields();
    
    $this->validatorSchema['username'] = new sfValidatorAnd(array(
      $this->validatorSchema['username'],
      new sfValidatorRegex(array('pattern' => '/^[\w\d\-\s@\.]+$/')),
    ));
    
    $this->validatorSchema['email'] = new sfValidatorAnd(array(
      $this->validatorSchema['email'],
      new sfValidatorEmail(),
    ));
    
    $this->embedForm('Profile', $this->getProfileForm($this->getObject()->Profile));
  }
  
  protected function getProfileForm(DmProfile $profile)
  {
    $profileForm = new DmProfileForm($profile);
    unset($profileForm['created_at'], $profileForm['updated_at'], $profileForm['user_id']);
    
    return $profileForm;
  }
}