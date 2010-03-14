<?php

/**
 * dmMailTemplate admin form
 *
 * @package    diem-commerce
 * @subpackage dmMailTemplate
 * @author     Your name here
 */
class DmMailTemplateAdminForm extends BaseDmMailTemplateForm
{
  public function configure()
  {
    parent::configure();

    $this->widgetSchema['subject'] = new sfWidgetFormInputText();

    $this->widgetSchema['body']->setAttribute('rows', 15);

    $this->widgetSchema['description']->setAttribute('rows', 2);
    
    // Unset automatic fields like 'created_at', 'updated_at', 'created_by', 'updated_by'
    $this->unsetAutoFields();
  }
}