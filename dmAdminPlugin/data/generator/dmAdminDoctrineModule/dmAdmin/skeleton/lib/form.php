<?php

/**
 * ##MODULE_NAME## admin form
 *
 * @package    ##PROJECT_NAME##
 * @subpackage ##MODULE_NAME##
 * @author     ##AUTHOR_NAME##
 */
class ##MODEL_CLASS##AdminForm extends Base##MODEL_CLASS##Form
{
  public function configure()
  {
    parent::configure();
    
    // Unset automatic fields like 'created_at', 'updated_at', 'created_by', 'updated_by'
    $this->unsetAutoFields();
  }
}