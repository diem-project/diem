<?php

require_once(sfConfig::get('dm_core_dir').'/lib/config/dmModuleManagerConfigHandler.php');

class dmFrontModuleManagerConfigHandler extends dmModuleManagerConfigHandler
{

  protected function fixModuleConfig($moduleKey, $moduleConfig, $isInProject)
  {
    $moduleOptions = parent::fixModuleConfig($moduleKey, $moduleConfig, $isInProject);

    $moduleOptions['sf_name'] = dmArray::get($moduleOptions, 'sf_name', $moduleKey);

    return $moduleOptions;
  }
  
  protected function fixSecurityConfig($moduleKey, $moduleConfig, $app = 'front')
  {
    return parent::fixSecurityConfig($moduleKey, $moduleConfig, $app);
  }
}