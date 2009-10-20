<?php

class dmActionLogComponents extends dmAdminBaseComponents
{
  
  public function executeLittle()
  {
    require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmActionLog/lib/dmActionLogViewLittle.php'));
    
    $this->log = new dmActionLogViewLittle($this->context->get('user_log'), $this->context->getI18n(), $this->getUser());
  }
  
}