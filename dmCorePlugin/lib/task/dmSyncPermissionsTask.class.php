<?php

class dmSyncPermissionsTask extends dmContextTask
{
  protected function configure()
  {
    parent::configure();

    $this->namespace        = 'dm';
    $this->name             = 'sync-permissions';
    $this->briefDescription = 'Sync modules-actions & records permissions';
    $this->detailedDescription = <<<EOF
The [dmSyncPermissions|INFO] task creates and syncs module-actions 
credentials and records credentials.
It creates in DB necessary permissions for records, and links permissions with
users and groups as described in modules.yml for each modules
Call it with:

  [php symfony dmSyncPermissions|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $this->logSection('diem', 'Synchronize permissions... This may take some time');
    $this->withDatabase();
    $modules = $this->get('module_manager')->getModules();
    $moduleSecurity = $this->get('module_security_manager');
    $moduleSecurity->setApp($options['application']);
    $securityStrateges = array();
    
    //@TODO add component, create strategy classes for it
    foreach(array('module') as $for)
    {
      $definedSecurityStrategies = $this->get('service_container')->getParameter(sprintf('%s_security.strategies', $for));
      foreach($definedSecurityStrategies as $securityStrategy)
      {
        $securityStrateges[$for][$securityStrategy] = $moduleSecurity->getStratege($securityStrategy, $for);
      }
    }

    foreach($modules as $module)
    {
      $securityYaml = $module->getOption('security');
      $securityYaml = isset($securityYaml[$options['application']]) ? $securityYaml[$options['application']] : false;
      if($securityYaml && !empty($securityYaml))
      {
        if(!empty($securityYaml['actions']))
        {
          foreach($securityYaml['actions'] as $actionName=>$actionConfig)
          {
            $securityStrateges['module'][$actionConfig['strategy']]->manageAuto($module, $actionName, $actionConfig);
          }
        }
        
        if(!empty($securityYaml['components']))
        {
          foreach($securityYaml['components'] as $component)
          {
            
          }
        }
      }
    }
  }
}
