<?php

class BaseDmUserAdminMyAccountForm extends BaseDmUserForm
{

  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();

    $this->widgetSchema['username'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['username'] = new sfValidatorChoice(array(
      'choices' => array($this->getObject()->username)
    ));

    $this->widgetSchema['old_password'] = new sfWidgetFormInputPassword(array(
      'label' => 'Old password'
    ));
    $this->validatorSchema['old_password'] = new sfValidatorString();

    $this->widgetSchema['password'] = new sfWidgetFormInputPassword(array(
      'label' => 'New password'
    ), array(
      'autocomplete' => 'off'
    ));
    $this->validatorSchema['password']->setOption('required', false);
    $this->widgetSchema['password_again'] = new sfWidgetFormInputPassword(array(
      'label' => 'New password (again)'
    ), array(
      'autocomplete' => 'off'
    ));
    $this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];

    $this->widgetSchema->moveField('password_again', 'after', 'password');

    $this->changeToEmail('email');

    $this->useFields(array('email', 'old_password', 'password', 'password_again'));

    $this->mergePreValidator(new sfValidatorCallback(array('callback' => array($this, 'currentUserValidator'))));

    $userValidator = new dmValidatorUser();
    $userValidator->setOption('password_field', 'old_password');
    $userValidator->setMessage('invalid', 'The password is invalid.');
    $this->mergePostValidator($userValidator);

    $this->mergePostValidator(new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'The two passwords must be the same.')));
  }

  public function currentUserValidator($validator, $values)
  {
    if (!$values['id'])
    {
      throw new sfValidatorErrorSchema($validator, array('email' => new sfValidatorError($validator, 'Do not use this form to create a user.')));
    }

    if ($values['id'] !== $this->object->id)
    {
      throw new sfValidatorErrorSchema($validator, array('email' => new sfValidatorError($validator, 'Do not change the user ID.')));
    }

    return $values;
  }
}