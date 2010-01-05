<?php

require_once(sfConfig::get('dm_core_dir').'/lib/config/dmModuleManagerConfigHandler.php');

class dmFrontModuleManagerConfigHandler extends dmModuleManagerConfigHandler
{

  protected function parse($configFiles)
  {
    parent::parse($configFiles);

    /*
     * Add the dmUser module if not present
     */
    if(!isset($this->modules['dmUser']))
    {
      $this->config['Internal'] = array(
        'User' => array(
          'dmUser' => $this->fixModuleConfig('dmUser', array(), false, false)
        )
      );
    }
  }
  
  protected function fixModuleConfig($moduleKey, $moduleConfig, $isInProject, $plugin)
  {
    $moduleOptions = parent::fixModuleConfig($moduleKey, $moduleConfig, $isInProject, $plugin);
    
    $moduleOptions['sf_name'] = dmArray::get($moduleOptions, 'sf_name', $moduleKey);
    
    return $moduleOptions;
  }

}