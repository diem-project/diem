<?php

require_once(sfConfig::get('dm_core_dir').'/lib/config/dmModuleManagerConfigHandler.php');

class dmAdminModuleManagerConfigHandler extends dmModuleManagerConfigHandler
{
  protected function fixModuleConfig($moduleKey, $moduleConfig, $isInProject)
  {
    $moduleOptions = parent::fixModuleConfig($moduleKey, $moduleConfig, $isInProject);
    
    return $moduleOptions;
  }
}