<?php

class DmForgotPasswordStep1Form extends dmForm
{
  public function configure()
  {
    $this->widgetSchema['email'] = new sfWidgetFormInputText();
    $this->validatorSchema['email'] = new sfValidatorEmail();
    
    $this->validatorSchema->setPostValidator(new sfValidatorCallback(array(
      'callback' => array($this, 'validateEmail')
    )));

    if ($this->isCaptchaEnabled())
    {
      $this->addCaptcha();
    }
  }

  public function validateEmail($validator, $values)
  {
    if ($values['email'] && !$this->getUserByEmail($values['email']))
    {
      throw new sfValidatorErrorSchema($validator, array('email' => new sfValidatorError($validator, 'This email does not exist.')));
    }

    return $values;
  }

  public function getUserByEmail($email)
  {
    return dmDb::table('DmUser')->retrieveByEmail($email);
  }

  public function addCaptcha()
  {
    $this->widgetSchema['captcha'] = new sfWidgetFormReCaptcha(array(
      'label'       => 'Captcha',
      'public_key'  => sfConfig::get('app_recaptcha_public_key')
    ));

    $this->validatorSchema['captcha'] = new sfValidatorReCaptcha(array(
      'private_key' => sfConfig::get('app_recaptcha_private_key')
    ));
  }

  public function isCaptchaEnabled()
  {
    return sfConfig::get('app_recaptcha_enabled');
  }
}