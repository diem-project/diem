<?php
/*
 * This file is part of the diem package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class (used as a service using sfServiceContainer) orchestrates
 * the management of security features for modules.
 *
 * Using modules.yml, you can declare which strategy to use to secure your
 * actions and components.
 *
 * Diem comes with two bundled action security strategies:
 *   - action: uses the symfony way to secure actions, using security.yml
 *         		within the config's directory in module directory
 *   - record: adds an overload to secure your actions. user calling a
 *   			module-action must have the right to do so. The rights are
 *   			managed using records of class DmSecureRecord, which
 *   			let you informs the module, action, model and primary key
 *   			of record to secure.
 *   			You can add secure-record permissions to groups and users
 *   			using the dedicated interface in System>Security>Records
 *   			in the admin application.
 *
 * @author serard
 *
 */
class dmModuleSecurityManager extends dmModuleSecurityAbstract implements dmModuleSecurityManagerInterface
{
  /**
   * Stores the strategies used within a secure() run.
   * @var array
   */
  protected $strategies = array();

  /**
   * @var dmModule
   */
  protected $module;

  /**
   * @var dmBaseActions
   */
  protected $action;
  
  /**
   * @var array
   */
  protected $recordsPermissions;

  /**
   * Secures a module according to its options.
   * We have to go through app/actions|components/config to run the correct
   * ->secure() method of the correct module securization strategy.
   * This is what the foreach loops are for.
   *
   * @param dmModule $module
   */
  public function secure(dmModule $module = null)
  {
    $this->clear();
    $this->module = $this->module ? $this->module : $module;
    $app = $this->getApplication();
    if($security = $module->getOption('security', false))
    {
      if(isset($security[$app]))
      {
        foreach($security[$app] as $actionKind=>$actionsConfig)
        {
          if(!is_array($actionsConfig)) continue;
          foreach($actionsConfig as $actionName=>$actionConfig)
          {
            if(!is_array($actionConfig)) continue;
            if(isset($actionConfig['is_secure']) || (!isset($actionConfig['is_secure']) && !empty($actionConfig['is_secure']) && count($actionConfig['is_secure']) > 0))
            {
              $this->getStrategy($actionConfig['strategy'], $actionKind)->secure($module, $actionName, $actionConfig);
            }
          }
        }
      }
    }elseif($credentials = $module->getOption('credentials', false)){
      if(is_string($credentials) && strlen($credentials) > 0)
      {
        $this->getStrategy('action', 'actions')->secure($module, 'all', array('is_secure'=>true, 'credentials'=>$credentials));
      }
    }
    $this->save();
  }

  /**
   * On each ->secure() run, we must clear the
   * existing strategies, so we can reset the running
   * module.
   */
  public function clear()
  {
  	parent::clear();
    $this->strategies = array();
  }

  /**
   * When the secure() process is over, we must save
   * what have to be saved for every used strategies.
   * This is mainly used so action strategy will not
   * overwrite many times the security.yml file.
   */
  protected function save()
  {
    foreach($this->strategies as $strategy)
    {
      $strategy->save();
    }
  }
  /**
   * (non-PHPdoc)
   * @see dmModuleSecurityManagerInterface::getStrategy()
   */
  public function getStrategy($strategy, $actionKind, $module = null, $action = null)
  {
    if(null === $strategy) { $strategy = 'action'; }
    $serviceStrategy = sprintf('module_security_%s_%s_strategy',$actionKind, $strategy);
    if(!isset($this->strategies[$serviceStrategy]))
    {
      $this->strategies[$serviceStrategy] = $this->container->getService($serviceStrategy)->setModule($this->module instanceof dmModule ? $this->module : $module)->setAction($action);
    }
    return $this->strategies[$serviceStrategy];
  }

  /**
   * Parses a yaml credential descriptor to its corresponding php.
   *
   * @param string $credentials
   * @return mixed array|string
   */
  public function parseCredentials($credentials)
  {
    $parser = new sfYamlParser();
    return $parser->parse($credentials);
  }

  /**
   * Sometimes dmModules don't have generate_dir set, so we must set it ourselves.
   * @todo moved to dmModule, more appropriate place
   * @param dmModule $module
   * @param unknown_type $configuration
   */
  public function setGenerateDirOption(dmModule $module, $configuration)
  {
    if ($pluginName = $module->getPluginName())
    {
      if($module->isOverridden())
      {
        return;
      }
      $module->setOption('generate_dir', dmOs::join($configuration->getPluginConfiguration($pluginName)->getRootDir(), 'modules', $module->getSfName()));
    }
    else
    {
      $module->setOption('generate_dir', dmOs::join(sfConfig::get('sf_apps_dir'), $this->getApplication().'/modules', $module->getSfName()));
    }
  }


  /**
   * Returns the security.yml as array
   * If file doesnt exist, returns an empty array
   *
   * @param dmModule $module
   * @return array the array representation of the security.yml file for the specified dmModule $module
   */
  public function getSecurityYaml(dmModule $module = null)
  {
  	if(null === $module){ $module = $this->module; }
    $yaml = array();
    if(file_exists($filepath = $this->getSecurityFilepath($module)))
    {
      $yaml = sfYaml::load($filepath);
    }
    return $yaml;
  }

  /**
   * Returns security.yml path for specified module
   *
   * @param dmModule $module
   * @return string the path to security.yml for specified module
   */
  public function getSecurityFilepath(dmModule $module = null)
  {
  	if(null === $module){ $module = $this->module; }
    return dmOs::join($module->getGenerationDir(), 'config', 'security.yml');
  }

  /**
   * Saves the yaml array by dumping it as yaml using sfYaml::dump
   * to the security.yml of the given $module
   *
   * @param dmModule $module the module
   * @param array $securityYaml the php array representation of the security.yml file
   */
  public function saveSecurityYaml(dmModule $module, $securityYaml)
  {
    $data = sfYaml::dump($securityYaml, 2);
    $filepath = $this->getSecurityFilepath($module);
    $dir = dirname($filepath);
    $this->container->get('filesystem')->mkdirs($dir);
    file_put_contents($filepath, $data);
  }

  /**
   * @param dmModule $module
   * @return dmModuleSecurityManager
   */
  public function setModule(dmModule $module)
  {
    $this->module = $module;
    return $this;
  }
  
  /**
   * @param dmBaseActions $action
   * @return dmModuleSecurityManager
   */
  public function setAction(dmBaseActions $action)
  {
    $this->action = $action;
    return $this;
  }

  /**
   * @param string $app
   * @param string $actionKind
   * @param string $action
   * @return boolean
   */
  public function hasSecurityConfiguration($app = null, $actionKind = null, $action = null)
  {
    if(null === $app){
      return $this->module->getOption('has_security', false);
    }else{
      $security = $this->module->getOption('security');
      $security = isset($security[$app]) ? $security[$app] : false;
    }
    if(!$security)
    {
    	return true; //this way, usual action strategy gets involved
    }
     
    if(null === $actionKind){
      return $security;
    }else{
      $security = isset($security[$actionKind]) ? $security[$actionKind] : false;
    }
    if(!$security) return false;
     
    if(null ===$action){
      return $security;
    }else{
      return isset($security[$action]) ? $security[$action] : ($this->module->getOption('credentials', false));
    }
    return false;
  }

  /**
   * @param string $app
   * @param string $actionKind
   * @param string $action
   * @return mixed false||array
   */
  public function getSecurityConfiguration($app = null, $actionKind = null, $action = null)
  {
    $security = $this->module->getOption('security');
    if(null === $app) return $security;

    $security = isset($security[$app]) ? $security[$app] : false;
    if(!$security){
      $credentials = $this->module->getOption('credentials');
      return array('strategy' => 'action', 'credentials' => $credentials, 'is_secure' => !empty($credentials));
    } 
     
    if(null === $actionKind){
      return $security;
    }else{
      $security = isset($security[$actionKind]) ? $security[$actionKind] : false;
    }
    if(!$security) return false;
     
    if(null ===$action){
      return $security;
    }else{
      return isset($security[$action]) ? $security[$action] : false;
    }
    return false;
  }

  /**
   * @param string $actionName
   * @return boolean
   */
  public function isActionStrategicalySecurized($actionName)
  {
    $user = $this->user->getUser();
    if($user && $user->get('is_super_admin')){
      return false;
    }
    $config = $this->hasSecurityConfiguration($this->getApplication(), 'actions', $actionName);
    return $config && isset($config['is_secure']) ? $config['is_secure'] : false;
  }

  /**
   * @param string $actionName
   * @return boolean
   */
  public function getActionSecurizationStrategy($actionName)
  {
    $actionSecurity = $this->getSecurityConfiguration($this->getApplication(), 'actions', $actionName);
    return $this->getStrategy($actionSecurity['strategy'], 'actions', $this->module, $this->action);
  }

  /**
   * @param string $action
   * @param dmDoctrineRecord $record
   */
  public function userHasCredentials($actionName, $record = null)
  {
    $user = $this->user->getUser();
    if($user && $user->get('is_super_admin')){
      return true;
    }
    if($this->hasSecurityConfiguration($this->getApplication(), 'actions', $actionName))
    {
    	return $this->getActionSecurizationStrategy($actionName)->userHasCredentials($actionName, $record);
    }
    return true;
  }
  
  /**
   * @param string $actionName
   * @param dmUser $user
   * @param array $ids
   */
  public function getIdsForAuthorizedActionWithinIds($actionName, $user, $ids)
  {
  	if($user->get('is_super_admin')) return $ids;
  	return $this->getActionSecurizationStrategy($actionName)->getIdsForAuthorizedActionWithinIds($actionName, $ids);
  }
}
