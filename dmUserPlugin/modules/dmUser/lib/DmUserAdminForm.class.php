<?php

/**
 * DmUserAdminForm for admin generators
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: DmUserAdminForm.class.php 23536 2009-11-02 21:41:21Z Kris.Wallsmith $
 */
class DmUserAdminForm extends BaseDmUserForm
{
  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();
    
    unset(
      $this['last_login'],
      $this['created_at'],
      $this['updated_at'],
      $this['salt'],
      $this['algorithm']
    );

    if (isset($this->widgetSchema['groups_list']))
    {
      $this->widgetSchema['groups_list']->setLabel('Groups');
    }
    if (isset($this->widgetSchema['permissions_list']))
    {
      $this->widgetSchema['permissions_list']->setLabel('Permissions');
    }
    
    $this->widgetSchema['password'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password']->setOption('required', false);
    $this->widgetSchema['password_again'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];

    $this->widgetSchema->moveField('password_again', 'after', 'password');
    
    $this->validatorSchema['username'] = new sfValidatorAnd(array(
      $this->validatorSchema['username'],
      new sfValidatorRegex(array('pattern' => '/^[\w\d\-\s@\.]+$/')),
    ));
    
    $this->validatorSchema['email'] = new sfValidatorAnd(array(
      $this->validatorSchema['email'],
      new sfValidatorEmail(),
    ));

    $this->mergePostValidator(new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'The two passwords must be the same.')));
  }
}
