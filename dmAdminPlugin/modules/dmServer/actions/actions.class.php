<?php

class dmServerActions extends dmAdminBaseActions
{

  public function executeIndex()
  {
  }

  public function executeApc()
  {
  }

  public function executeIncludeApc()
  {
    define('USE_AUTHENTICATION', 0);
    require(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/apc/monitor.php'));
    die;
  }
  
  public function executePhpinfo()
  {
    phpinfo();
    die;
  }
  
  public function executeCheck()
  {
    dm::checkServer();
    die;
  }
}
