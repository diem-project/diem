<?php

require_once(dm::getDir().'/dmAdminPlugin/lib/config/dmAdminApplicationConfiguration.php');

class adminConfiguration extends dmAdminApplicationConfiguration
{
  public function configure()
  {
    
  }
  
  public function setCacheDir($cacheDir)
  {
    $cacheDir = sys_get_temp_dir().'/dm_test_cache';
    
    if (!is_dir($cacheDir))
    {
      mkdir($cacheDir);
    }
    
    return parent::setCacheDir($cacheDir);
  }
}