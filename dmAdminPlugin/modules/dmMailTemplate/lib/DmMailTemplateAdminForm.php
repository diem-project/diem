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
    
    // Unset automatic fields like 'created_at', 'updated_at', 'created_by', 'updated_by'
    $this->unsetAutoFields();
  }
}