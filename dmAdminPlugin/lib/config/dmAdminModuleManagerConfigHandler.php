<?php

require_once(sfConfig::get('dm_core_dir').'/lib/config/dmModuleManagerConfigHandler.php');

class dmAdminModuleManagerConfigHandler extends dmModuleManagerConfigHandler
{
  protected function fixModuleConfig($moduleKey, $moduleConfig, $isInProject)
  {
    $moduleOptions = parent::fixModuleConfig($moduleKey, $moduleConfig, $isInProject);

    $moduleOptions['admin'] = !$moduleOptions['is_project'] || file_exists(dmOs::join(sfConfig::get('sf_app_module_dir'), $moduleKey, 'actions/actions.class.php'));

    return $moduleOptions;
  }
}