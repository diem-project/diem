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
	 * @var dmModuleActionSecurityStrategy
	 */
	protected $moduleActionStrategy;
	
	/**
	 * @var dmModuleActionRecordSecurityStrategy
	 */
	protected $moduleActionRecordStrategy;
	
	public function __construct($context, $container)
	{
		parent::__construct($context, $container);
		$this->moduleActionStrategy = new dmModuleActionSecurityStrategy($context, $container);
		$this->moduleActionRecordStrategy = new dmModuleActionRecordSecurityStrategy($context, $container);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see dmModuleSecurityStrategyAbstract::secure()
	 */
	public function secure(dmModule $module, $app, $actionName, $actionConfig)
	{
		$this->moduleActionStrategy->secure($module, $app, $actionName, $actionConfig);
	}
	
	public function save()
	{
		$this->moduleActionStrategy->save();
	}
	
	public function userHasCredentials($user)
	{
		return $this->moduleActionStrategy->userHasCredentials($user)
	}
}