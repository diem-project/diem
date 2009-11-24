<?php

require_once(sfConfig::get('dm_core_dir').'/lib/config/dmModuleManagerConfigHandler.php');

class dmFrontModuleManagerConfigHandler extends dmModuleManagerConfigHandler
{
  protected function fixModuleConfig($moduleKey, $moduleConfig, $isInProject)
  {
    $moduleOptions = parent::fixModuleConfig($moduleKey, $moduleConfig, $isInProject);
    
    foreach(dmArray::get($moduleConfig, 'actions', array()) as $actionKey => $actionConfig)
    {
      if(is_array($actionConfig) && array_key_exists('filters', $actionConfig) && !is_array($actionConfig['filters']))
      {
        $moduleConfig['actions'][$actionKey]['filters'] = array($actionConfig['filters']);
      }
    }
    
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
      
      $moduleOptions['actions'] = (array) dmArray::get($moduleConfig, 'actions', array());
    }
    
    return $moduleOptions;
  }


  protected function getExportedModuleOptions($key, $options)
  {
    if ($options['is_project'])
    {
      $actionsConfig = $options['actions'];
      
      $options['actions'] = '__DM_MODULE_ACTIONS_PLACEHOLDER__';
      
      $exported  = parent::getExportedModuleOptions($key, $options);
      
      $actions = 'array(';

      foreach($actionsConfig as $actionKey => $actionConfig)
      {
        if (is_integer($actionKey))
        {
          $actionKey = $actionConfig;
          $actionConfig = array();
        }
        
        if (empty($actionConfig['name']))
        {
          $actionConfig['name'] = dmString::humanize($actionKey);
        }
    
        if (empty($actionConfig['type']))
        {
          if (strncmp($actionKey, 'list', 4) === 0)
          {
            $actionConfig['type'] = 'list';
          }
          elseif (strncmp($actionKey, 'show', 4) === 0)
          {
            $actionConfig['type'] = 'show';
          }
          elseif (strncmp($actionKey, 'form', 4) === 0)
          {
            $actionConfig['type'] = 'form';
          }
          else
          {
            $actionConfig['type'] = 'simple';
          }
        }
        
        $actions .= sprintf('\'%s\' => new dmAction(\'%s\', %s), ', $actionKey, $actionKey, var_export($actionConfig, true));
      }

      $actions .= ')';
      
      $exported = str_replace('\'__DM_MODULE_ACTIONS_PLACEHOLDER__\'', $actions, $exported);
    }
    else
    {
      $exported  = parent::getExportedModuleOptions($key, $options);
    }

    return $exported;
  }
}