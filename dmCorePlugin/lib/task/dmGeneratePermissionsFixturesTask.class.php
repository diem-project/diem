<?php

/**
 * Generates DmPermission Fixtures for securize modules
 */
class dmGeneratePermissionsFixturesTask extends dmContextTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->namespace = 'dm';
    $this->name = 'generate-permissions-fixtures';
    $this->briefDescription = 'Generates DmPermission fixtures for securized admin modules';

    $this->detailedDescription = <<<EOF
Will create DmPermission fixtures files for securized admin modules
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->logSection('diem', 'Generate fixtures for DmPermissions within admin modules');
    $modules = $this->get('module_manager')->getModules();
    unset($modules['dmUser'], $modules['dmPermission'], $modules['dmGroup'], $modules['dmRecordPermission'], 
    $modules['dmRecordPermissionAssociation']);

    foreach($modules as $moduleKey => $module)
    {
      $securityDescriptor = $module->getSecurityManager()->getSecurityConfiguration();
      if(!empty($securityDescriptor))
      {
      	$this->logSection('diem', 'generating DmPermission fixtures for ' . $module->getKey());
      	$this->writeFixture($module, $securityDescriptor);
      }
    }
  }
  
  protected function clear()
  {
  	$this->permissions = array();
  }
  
  protected function writeFixture(dmModule $module, $securityDesc)
  {
  	$this->clear();
  	if($pluginName = $module->getPluginName())
  	{
  		$root = dmContext::getInstance()->getConfiguration()->getPluginConfiguration($pluginName)->getRootDir();
  	}
  	else{
  		$root = sfConfig::get('sf_root_dir');
  	}
  	$fixturesRootPath = dmOs::join($root, 'data', 'fixtures');
  	foreach(array('admin', 'front') as $app)
  	{
  		foreach(array('actions', 'components') as $actionKind)
  		{
  			if(isset($securityDesc[$app]) && isset($securityDesc[$app][$actionKind]) && is_array($securityDesc[$app][$actionKind]))
  			{
  				foreach($securityDesc[$app][$actionKind] as $actionName=>$actionDesc)
  				{
  					if(isset($actionDesc['credentials']))
  					{
  						$credentials = (array) $module->getSecurityManager()->parseCredentials($actionDesc['credentials']);
  						foreach($credentials as $credential)
  						{
  							$this->addPermissionFor($credential, $module->getKey(), $actionName);
  						}
  					}
  				}
  			}
  		}
  	}
  	$this->doWriteFixture(dmOs::join($fixturesRootPath, 'DmPermissions', $module->getKey() . '.yml'));
  }
  
  protected function doWriteFixture($file)
  {
  	if(empty($this->permissions)) return;
  	$permissions = array('DmPermission'=>$this->permissions);
  	$dumped = new sfYamlDumper();
  	$fixture = $dumped->dump($permissions, 5);
  	$this->getFilesystem()->mkdirs(dirname($file));
  	file_put_contents($file, $fixture);
  	$this->logSection('file+', $file);
  }
  
  protected function addPermissionFor($credential, $module, $action)
  {
  	$dmPermissionName = 'DmPermission_' . str_replace('/', '_', dmString::modulize($credential));
  	$this->permissions[$dmPermissionName] = array('name' => $credential, 'description' => sprintf('Grant access to action %s of module %s', $action, $module));
  }
}
