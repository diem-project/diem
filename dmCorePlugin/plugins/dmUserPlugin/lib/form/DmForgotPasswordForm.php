<?php

class DmForgotPasswordForm extends dmForm
{
  public function configure()
  {
    $this->widgetSchema['email'] = new sfWidgetFormInputText();
    $this->validatorSchema['email'] = new sfValidatorEmail();
    
    $this->validatorSchema->setPostValidator(new sfValidatorCallback(array(
      'callback' => array($this, 'validateEmail')
    )));
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
}