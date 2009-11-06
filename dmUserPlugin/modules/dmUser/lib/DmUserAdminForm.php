<?php

/**
 * DmUserAdminForm for admin generators
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: DmUserAdminForm.class.php 23536 2009-11-02 21:41:21Z Kris.Wallsmith $
 */
class DmUserAdminForm extends BaseDmUserAdminForm
{
  
  public function setup()
  {
    parent::setup();
    
    $this->useFields(array('username', 'email', 'password', 'password_again', 'is_active', 'is_super_admin', 'groups_list', 'permissions_list'));
  }
  
}
