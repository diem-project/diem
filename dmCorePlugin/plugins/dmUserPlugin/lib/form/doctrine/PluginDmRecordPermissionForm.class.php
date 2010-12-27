<?php

/**
 * PluginDmRecordPermission form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id$
 */
abstract class PluginDmRecordPermissionForm extends BaseDmRecordPermissionForm
{
  public function setup()
  {
    parent::setup();
    /*
     * Here, the plugin form code
     */

    $this->widgetSchema['see_admin'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['see_front'] = new sfWidgetFormInputHidden();

    $this->validatorSchema['see_admin'] = new sfValidatorPass();
    $this->validatorSchema['see_front'] = new sfValidatorPass();
  }
}