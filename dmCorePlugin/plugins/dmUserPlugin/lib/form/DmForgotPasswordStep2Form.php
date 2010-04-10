<?php

class DmForgotPasswordStep2Form extends dmForm
{
  protected $user;

  public function __construct(DmUser $user, array $options = array())
  {
    $this->user = $user;

    parent::__construct(array(), $options);
  }
  
  public function configure()
  {
    $this->widgetSchema['password'] = new sfWidgetFormInputPassword(array(), array(
      'autocomplete' => 'off'
    ));
    $this->validatorSchema['password'] = new sfValidatorString(array('max_length' => 128));
    
    $this->widgetSchema['password_again'] = new sfWidgetFormInputPassword(array(
      'label' => 'Password (again)'
    ), array(
      'autocomplete' => 'off'
    ));
    $this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];
    
    $this->mergePostValidator(new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'The two passwords must be the same.')));
  }

  /**
   * @return DmUser
   */
  public function getUser()
  {
    return $this->user;
  }
}