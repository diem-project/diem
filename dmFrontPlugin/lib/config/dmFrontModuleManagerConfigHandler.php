<?php

require_once(sfConfig::get('dm_core_dir').'/lib/config/dmModuleManagerConfigHandler.php');

class dmFrontModuleManagerConfigHandler extends dmModuleManagerConfigHandler
{
  protected function fixModuleConfig($moduleKey, $moduleConfig, $isInProject)
  {
    $moduleOptions = parent::fixModuleConfig($moduleKey, $moduleConfig, $isInProject);
    
    if ($moduleOptions['is_project'])
    {
      $directActions = array();
        
      $actionsFile = dmOs::join(sfConfig::get('sf_app_module_dir'), $moduleKey, 'actions/actions.class.php');
      
      if (file_exists($actionsFile))
      {
        require_once($actionsFile);
        
        $actionsClass = $moduleKey.'Actions';
        
        foreach(get_class_methods($actionsClass) as $method)
        {
          if (preg_match('|^execute[\w\d]+$|', $method) && $directActionMethodName = dmString::modulize(substr($method, 7)))
          {
            $directActions[] = $directActionMethodName;
          }
        }
      }
      
      $moduleOptions['direct_actions'] = $directActions;
    }
    
    return $moduleOptions;
  }
}