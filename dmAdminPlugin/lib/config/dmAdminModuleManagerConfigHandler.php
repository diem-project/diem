<?php

require_once(sfConfig::get('dm_core_dir').'/lib/config/dmModuleManagerConfigHandler.php');

class dmAdminModuleManagerConfigHandler extends dmModuleManagerConfigHandler
{
  
  protected function fixModuleConfig($moduleKey, $moduleConfig, $isInProject)
  {
    $moduleOptions = parent::fixModuleConfig($moduleKey, $moduleConfig, $isInProject);
    
    $moduleOptions['sf_name'] = dmArray::get($moduleOptions, 'sf_name',
      ($moduleOptions['plugin'] && 'dmAdminPlugin' !== $moduleOptions['plugin']) ? $moduleKey.'Admin' : $moduleKey
    );
    
    return $moduleOptions;
  }
  
  protected function fixSecurityConfig($moduleKey, $moduleConfig, $app = 'admin')
  {
    return parent::fixSecurityConfig($moduleKey, $moduleConfig, $app);
  }

}