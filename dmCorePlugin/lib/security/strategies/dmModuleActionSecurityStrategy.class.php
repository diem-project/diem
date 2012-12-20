<?php
/*
 *
 */

/**
 *
 * @author serard
 *
 */
class dmModuleActionSecurityStrategy extends dmModuleSecurityStrategyAbstract implements dmModuleSecurityStrategyInterface
{

	/**
	 * This method is responsible for securing a module-action using symfony security.yml file.
	 * It is called when generating modules using dm(Admin|Front):generate or :generate-module.
	 *
	 * (non-PHPdoc)
	 * @see dmModuleSecurityStrategyAbstract::secure()
	 */
	public function secure(dmModule $module, $actionName, $actionConfig)
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

	public function userHasCredentials($actionName = null)
	{
		$actionName = null === $actionName ? $this->action->getActionName() : $actionName;
		$cacheKey = sprintf('%s/%s/%s/%s', $this->getApplication(), $this->container->getService('user')->getUser()->get('id'), $this->module->getUnderscore(), $actionName);
		if(!$this->has($cacheKey)){
			$credentials = $this->getCredentials($actionName);
			if($credentials){
				$result = $this->user->can($credentials) || $this->user->hasCredential($credentials);
			}else{ $result = false; }
			$this->set($cacheKey, $result);
			
			return $result;
		}
		return $this->get($cacheKey);
	}

	public function getCredentials($actionName = null)
	{
		$security = $this->module->getSecurityManager()->getSecurityConfiguration($this->module->getSecurityManager()->getApplication(), 'actions', $actionName);
		$credentials = isset($security['is_secure']) && false === $security['is_secure'] ? false : (isset($security['credentials']) ? $security['credentials'] : null);
		
		return $credentials;
	}

	/**
	 * @param dmModule $module
	 */
	protected function getSecurityYaml()
	{
		if(!$this->has('securityYaml'))
		{
			return $this->set('securityYaml', $this->module->getSecurityManager()->getSecurityYaml($this->module));
		}
		return $this->get('securityYaml');
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
		$this->set('securityYaml', $securityYaml);
	}

	public function addPermissionCheckToQuery($query)
	{
		//do nothing
	}

	public function getIdsForAuthorizedActionWithinIds($actionName, $ids)
	{
		return $this->userHasCredentials($actionName) ? $ids : false;
	}
}