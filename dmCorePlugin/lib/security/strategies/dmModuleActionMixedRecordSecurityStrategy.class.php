<?php
/*
 *
 *
 */

/**
 * This class is a module-action & module-action-record security strategy.
 *
 * It will check if user has necessary credentials to execute the action using
 * simple $user->hasCredentials() and it will take care of returning
 * only authorized records user can manage.
 *
 * This security strategy is usefull when user can access the interface given
 * by a module-action, having this module-action let user manage lot of
 * records. Typically, a list of records does this.
 *
 * So you'll be able to specify credentials needed to enter the
 * interface, but manageable objects will depends on the user's associated
 * dmRecordPermissions.
 *
 * @author serard
 *
 */
class dmModuleActionMixedRecordSecurityStrategy extends dmModuleSecurityStrategyAbstract implements dmModuleSecurityStrategyInterface
{

	/**
	 * @var dmModuleSecurityManager
	 */
	protected $manager;

	/**
	 * @var dmModuleActionSecurityStrategy
	 */
	protected $actionStrategy;


	/**
	 * @var dmModuleActionRecordSecurityStrategy
	 */
	protected $moduleActionRecordStrategy;

	public function __construct($context, $container, $user)
	{
		parent::__construct($context, $container, $user);
		$this->manager = $this->container->getService('module_security_manager');
	}

	/**
	 * (non-PHPdoc)
	 * @see dmModuleSecurityStrategyAbstract::secure()
	 */
	public function secure(dmModule $module, $actionName, $actionConfig)
	{
		$this->actionStrategy = $this->manager->getStrategy('action', 'actions', $this->module, $this->action);
		$this->actionStrategy->secure($module, $actionName, $actionConfig);
	}

	public function save()
	{
		$this->actionStrategy->save();
	}

	public function userHasCredentials($actionName = null, $record = null)
	{
		if($actionName === null )
		{
			$actionName = $this->action->getName();
		}
		$strategy = $actionName !== null && $record === null ? 'action' : ($record === null ? 'action' : 'record');
		if($strategy === 'record' && null === $record)
		{
			$record = $this->action->getObject();
		}
		return $this->manager->getStrategy($strategy, 'actions', $this->module, $this->action)->userHasCredentials($actionName, $record);
	}

	public function getCredentials($actionName, $record = null)
	{
		if($actionName === null )
		{
			$actionName = $this->action->getName();
		}
		
		$strategy = $actionName !== null && $record === null ? 'action' : ($record === null ? 'action' : 'record');
		if($strategy === 'record' && null === $record)
		{
			$record = $this->action->getObject();
		}
		return $this->manager->getStrategy($strategy, 'actions', $this->module, $this->action)->getCredentials($actionName, $record);
	}

	public function addPermissionCheckToQuery($query)
	{
		return $this->manager->getStrategy('record', 'actions', $this->module, $this->action)->addPermissionCheckToQuery($query);
	}

	public function getIdsForAuthorizedActionWithinIds($ids)
	{
		return $this->manager->getStrategy('record', 'actions', $this->module, $this->action)->getIdsForAuthorizedActionWithinIds($ids);
	}
}