<?php

require_once(dm::getDir().'/dmFrontPlugin/lib/config/dmFrontApplicationConfiguration.php');

class frontConfiguration extends dmFrontApplicationConfiguration
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