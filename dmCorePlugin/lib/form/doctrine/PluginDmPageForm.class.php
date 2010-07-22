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
  public function setup()
  {
    parent::setup();
    
    $templates = $this->getTemplates();

    $this->widgetSchema['template'] = new sfWidgetFormChoice(array(
      'choices' => dmArray::valueToKey($templates)
    ));

    $this->validatorSchema['template'] = new sfValidatorChoice(array(
      'choices' => $templates
    ));
  }

  public function getTemplates()
  {
    $files = $this->getService('filesystem')
    ->find('file')
    ->name('*Success.php')
    ->in(sfConfig::get('sf_root_dir').'/apps/front/modules/dmFront/templates');

    $templates = array();
    foreach($files as $file)
    {
      $templates[] = str_replace('Success.php', '', basename($file));
    }

    return $templates;
  }
  
  protected function getAutoFieldsToUnset()
  {
    return array('created_at', 'updated_at');
  }
}
