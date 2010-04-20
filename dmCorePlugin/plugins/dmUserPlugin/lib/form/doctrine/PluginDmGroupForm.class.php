<?php

/**
 * PluginDmGroup form.
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: PluginDmGroupForm.class.php 23536 2009-11-02 21:41:21Z Kris.Wallsmith $
 */
abstract class PluginDmGroupForm extends BaseDmGroupForm
{
  /**
   * @see sfForm
   */
  protected function setupInheritance()
  {
    parent::setupInheritance();

    unset(
      $this['group_list'],
      $this['created_at'],
      $this['updated_at']
    );

    $this->widgetSchema['permissions_list']->setLabel('Permissions');
  }
}
