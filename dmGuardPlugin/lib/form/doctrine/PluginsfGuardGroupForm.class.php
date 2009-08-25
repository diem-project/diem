<?php

/**
 * PluginsfGuardGroup form.
 *
 * @package    form
 * @subpackage sfGuardGroup
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class PluginsfGuardGroupForm extends BasesfGuardGroupForm
{
  public function configure()
  {
    unset($this['sf_guard_user_group_list']);

    $this->widgetSchema['sf_guard_group_permission_list']->setLabel('Permissions');
  }
}