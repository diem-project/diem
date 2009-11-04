<?php

class BaseDmFormSignin extends BaseForm
{
  /**
   * @see sfForm
   */
  public function setup()
  {
    $this->setWidgets(array(
      'username' => new sfWidgetFormInputText(),
      'password' => new sfWidgetFormInputPassword(array('type' => 'password')),
      'remember' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'username' => new sfValidatorString(),
      'password' => new sfValidatorString(),
      'remember' => new sfValidatorBoolean(),
    ));

    $this->validatorSchema->setPostValidator(new dmValidatorUser());

    $this->widgetSchema->setNameFormat('signin[%s]');

    $this->widgetSchema->setFormFormatterName('dmList');

    $this->setDefault('remember', true);
  }
}
