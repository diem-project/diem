<?php
/*
 *
 */

/**
 *
 * Enter description here ...
 * @author serard
 *
 */
class dmModuleActionSecurityStrategy extends dmModuleSecurityStrategy
{
  public function secure(dmModule $module, $actionName, $actionConfig)
  {
    $securityYaml = $this->getSecurityYaml($module);
    $securityYaml[$actionName]['is_secure'] = $actionConfig['is_secure'];
    if(isset($actionConfig['credentials']))
    {
      $securityYaml[$actionName]['credentials'] = $actionConfig['credentials'];
    }
    $this->saveSecurityYaml($module, $securityYaml);
  }

  public function manageAuto(dmModule $module, $actionName, $actionConfig)
  {
    $this->associatePermissionWithGroupsAndUsers($actionConfig);
  }

  protected function associatePermissionWithGroupsAndUsers($actionConfig)
  {
    if(isset($actionConfig['auto']))
    {
      if(isset($actionConfig['auto']['groups']) && !empty($actionConfig['auto']['groups']))
      {
        foreach($actionConfig['auto']['groups'] as $group)
        {
          $group = Doctrine_Core::getTable('DmGroup')->findOneBy('name', $group);
          if($group)
          {
            $this->associateGroupWithPermissions($actionConfig['credentials'], $group);
          }
          $group->save();
        }
      }
      if(isset($actionConfig['auto']['users']) && !empty($actionConfig['auto']['users']))
      {

      }
    }
  }
  
  protected function associateGroupWithPermissions($credentials, DmGroup $group)
  {
    if(!is_array($credentials)){
      $credentials = array($credentials);
    }
    $this->doAssociateGroupWithPermissions($credentials, $group);
  }
  
  protected function doAssociateGroupWithPermissions($credentials, DmGroup $group)
  {
    foreach($credentials as $credential)
    {
      if(is_array($credential))
      {
        $this->doAssociateGroupWithPermissions($credential, $group);
      }
      else{
        $this->doAssociateGroupWithPermission($credential, $group);
      }
    }
  }

  protected function doAssociateGroupWithPermission($credential, DmGroup $group)
  {
    $permission = Doctrine_Core::getTable('DmPermission')->findOneBy('name', $credential);
    $group->get('Permissions')->add($permission);
  }
  
  protected function getSecurityYaml(dmModule $module)
  {
    return $this->container->get('module_security_manager')->getSecurityYaml($module);
  }

  /**
   * Returns security.yml path for specified module
   *
   * @param dmModule $module
   * @return string the path to security.yml for specified module
   */
  protected function getSecurityFilepath(dmModule $module)
  {
    return $this->container->get('module_security_manager')->getSecurityFilepath($module);
  }

  /**
   * Saves the security.yml array representation to
   * security.yml
   *
   * @param dmModule $module
   * @param array $securityYaml
   */
  protected function saveSecurityYaml(dmModule $module, $securityYaml)
  {
    $data = sfYaml::dump($securityYaml, 2);
    file_put_contents($this->getSecurityFilepath($module), $data);
  }
}