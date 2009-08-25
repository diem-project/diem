<?php

/**
 * BasesfGuardUserAdminForm
 *
 * @package form
 * @subpackage sf_guard_user
 */
class BasesfGuardUserAdminForm extends BasesfGuardUserForm
{
  public function configure()
  {
    unset(
      $this['last_login'],
      $this['created_at'],
      $this['salt'],
      $this['algorithm']
    );

    $this->widgetSchema['groups_list']->setLabel('Groups');
    $this->widgetSchema['groups_list']->addOption('expanded', true);
    
    $this->widgetSchema['permissions_list']->setLabel('Permissions');
    $this->widgetSchema['permissions_list']->addOption('expanded', true);

    $this->widgetSchema['password'] = new sfWidgetFormInputPassword();
    $this->widgetSchema['password']->setAttribute('autocomplete', 'off');
    $this->validatorSchema['password']->setOption('required', false);
    
    $this->widgetSchema['password_again'] = new sfWidgetFormInputPassword();
    $this->widgetSchema['password_again']->setAttribute('autocomplete', 'off');
    $this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];

    $this->widgetSchema->moveField('password_again', 'after', 'password');

    $this->mergePostValidator(new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'The two passwords must be the same.')));
  }
}