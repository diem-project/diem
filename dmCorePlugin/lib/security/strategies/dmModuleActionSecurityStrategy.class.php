<?php
/*
 *
 */

/**
 *
 * @author serard
 *
 */
class dmModuleActionSecurityStrategy extends dmModuleSecurityStrategyAbstract
{

  /**
   * This method is responsible for securing a module-action using symfony security.yml file.
   * It is called when generating modules using dm(Admin|Front):generate or :generate-module.
   * 
   * (non-PHPdoc)
   * @see dmModuleSecurityStrategyAbstract::secure()
   */
  public function secure(dmModule $module, $app, $actionName, $actionConfig)
  {
    $securityYaml = $this->getSecurityYaml($module);
    $securityYaml[$actionName]['is_secure'] = $actionConfig['is_secure'];
    if(isset($actionConfig['credentials']))
    {
      $securityYaml[$actionName]['credentials'] = $actionConfig['credentials'];
    }
    $this->saveSecurityYaml($securityYaml);
  }
  
  public function save()
  {
    $this->container->get('module_security_manager')->saveSecurityYaml($this->module, $this->getCache('securityYaml'));
    parent::save();
  }

  /**
   * 
   * Enter description here ...
   * @param dmModule $module
   */
  protected function getSecurityYaml()
  {
    if(!$this->hasCache('securityYaml'))
    {
     $this->setCache('securityYaml', $this->container->get('module_security_manager')->getSecurityYaml($this->module)); 
    }
    return $this->getCache('securityYaml');
  }

  /**
   * Returns security.yml path for specified module
   *
   * @param dmModule $module
   * @return string the path to security.yml for specified module
   */
  protected function getSecurityFilepath()
  {
    return $this->container->get('module_security_manager')->getSecurityFilepath($this->module);
  }

  /**
   * Saves the security.yml array representation to
   * security.yml
   *
   * @param dmModule $module
   * @param array $securityYaml
   */
  protected function saveSecurityYaml($securityYaml)
  {
    $this->setCache('securityYaml', $securityYaml);
  }
  
  
  
  
  
  
  
  public function addPermissionCheckToQuery($query)
  {
  	$do = "niothgin";
  }
}