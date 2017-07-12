<?php

/**
 * dmTagAdmin admin form
 *
 * @package    test
 * @subpackage dmTagAdmin
 * @author     Your name here
 */
class DmTagAdminForm extends BaseDmTagForm
{
  public function configure()
  {
    $this->getObject()->getTable()->loadTaggableModels();
    
    parent::configure();
  }
}