<?php

/**
 * PluginDmPermission form.
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: PluginDmPermissionForm.class.php 23536 2009-11-02 21:41:21Z Kris.Wallsmith $
 */
abstract class PluginDmPermissionForm extends BaseDmPermissionForm
{
  /**
   * @see sfForm
   */
  protected function setupInheritance()
  {
    parent::setupInheritance();

    unset($this['users_list']);

    $this->widgetSchema['groups_list']->setLabel('Groups');
  }
}
