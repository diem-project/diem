<?php

/**
 * dmRecordPermissionAssociationAdmin admin form
 *
 * @package    diem-5.4-security
 * @subpackage dmRecordPermissionAssociationAdmin
 * @author     Your name here
 */
class DmRecordPermissionAssociationAdminForm extends BaseDmRecordPermissionAssociationForm
{
  public function configure()
  {
    parent::configure();
    /*$this->widgetSchema['dm_secure_module'] = new sfWidgetFormDmModules();
    $this->validatorSchema['dm_secure_module'] = new dmValidatorDmModules();

    $this->widgetSchema['dm_secure_action'] = new sfWidgetFormDmActions();
    $this->validatorSchema['dm_secure_action'] = new dmValidatorDmActions();
    */
  }
}