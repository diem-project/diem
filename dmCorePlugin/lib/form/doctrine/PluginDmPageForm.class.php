<?php

/**
 * PluginDmPage form.
 *
 * @package    form
 * @subpackage DmPage
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class PluginDmPageForm extends BaseDmPageForm
{
  
  public function getAutoFieldsToUnset()
  {
    return array('created_at', 'updated_at');
  }
}