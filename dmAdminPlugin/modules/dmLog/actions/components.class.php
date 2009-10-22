<?php

class dmLogComponents extends dmAdminBaseComponents
{
  
  public function executeLittle()
  {
    $this->logKey = $this->name;
    
    $this->log = $this->context->get($this->name.'_log');
    
    $logViewClass = get_class($this->log).'ViewLittle';
    require_once(dmOs::join(sfConfig::get('dm_admin_dir'), 'modules/dmLog/lib/'.$logViewClass.'.php'));
    
    $this->logView = new $logViewClass($this->log, $this->context->getI18n(), $this->getUser());
  }
  
}