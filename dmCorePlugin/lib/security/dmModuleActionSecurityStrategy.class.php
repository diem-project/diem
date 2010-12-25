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
class dmModuleActionSecurityStrategy extends dmMicroCache
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
    $this->createCredentialsRecords($actionConfig['credentials']);
    if(isset($actionConfig['auto']))
    {
      $this->manageAuto($actionConfig['auto']);
    }
  }

  protected function createCredentialsRecords($credentials)
  {
    
  }
  
  protected function manageAuto($toAuto)
  {
    
  }

  /**
   * Returns the security.yml as array
   * If file doesnt exist, returns an empty array
   * @param dmModule $module
   * @return array the array representation of the security.yml file for the specified dmModule $module
   */
  protected function getSecurityYaml(dmModule $module)
  {
    if($this->hasCache('yaml'))
    {
      return $this->getCache('yaml');
    }

    $yaml = array();
    if(file_exists($filepath = $this->getSecurityFilepath($module)))
    {
      $yaml = sfYaml::load($filepath);
    }
    $this->setCache('yaml', $yaml);
    return $yaml;
  }

  /**
   * Returns security.yml path for specified module
   *
   * @param dmModule $module
   * @return string the path to security.yml for specified module
   */
  protected function getSecurityFilepath(dmModule $module)
  {
    return dmOs::join($module->getOption('generate_dir'), 'config', 'security.yml');
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
    $this->setCache('yaml', $securityYaml);
    $data = sfYaml::dump($securityYaml, 2);
    file_put_contents($this->getSecurityFilepath($module), $data);
  }
}