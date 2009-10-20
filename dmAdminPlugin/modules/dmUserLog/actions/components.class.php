<?php

class dmUserLogComponents extends dmAdminBaseComponents
{
  
  public function executeLittle()
  {
    require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmUserLog/lib/dmUserLogViewLittle.php'));
    
    $this->log = new dmUserLogViewLittle($this->context->get('user_log'), $this->context->getI18n(), $this->getUser());
  }
  
}