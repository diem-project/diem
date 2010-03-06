<?php

class BaseDmUserAdminMyAccountForm extends DmUserForm
{

  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();

    $this->widgetSchema['password'] = new sfWidgetFormInputPassword(array(), array(
      'autocomplete' => 'off'
    ));
    $this->validatorSchema['password']->setOption('required', false);
    $this->widgetSchema['password_again'] = new sfWidgetFormInputPassword(array(
      'label' => 'Password (again)'
    ), array(
      'autocomplete' => 'off'
    ));
    $this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];

    $this->widgetSchema->moveField('password_again', 'after', 'password');

    $this->changeToEmail('email');

    $this->useFields(array('email', 'password', 'password_again'));

    $this->mergePreValidator(new sfValidatorCallback(array('callback' => array($this, 'currentUserValidator'))));

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