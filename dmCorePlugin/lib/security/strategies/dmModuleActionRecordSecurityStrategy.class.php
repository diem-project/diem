<?php
/*
 *
 */

/**
 * This class is a securization strategy for modules, actions and records.
 * 
 * It helps you secure the execution of a module-action for given records.
 * By associating a dmUser or its dmGroups to a given dmRecordPermission, 
 * you ensure user will have right to execute the given module-action for
 * the given record.
 * 
 * This simple yet kind of complex securization strategy is not so helpfull
 * when it comes to secure the access to a list.
 * Because this strategy will find the permissions for the given module-action, 
 * if none exists (let say there are no records), you won't give user access
 * to the module-action list, thus he will not be able to create new records, 
 * as in the Diem sense, action index is listing of records.
 * 
 * To overcome this problem, there is another strategy called
 * dmModuleActionMixedRecordSecurityStrategy.
 * 
 *
 *
 * @author serard
 *
 */
class dmModuleActionRecordSecurityStrategy extends dmModuleSecurityStrategyAbstract implements dmModuleSecurityStrategyInterface
{

	/**
	 * With this strategy, there's nothing to do on module generation
	 *
	 * (non-PHPdoc)
	 * @see dmModuleSecurityStrategyAbstract::secure()
	 */
	public function secure(dmModule $module, $app, $actionName, $actionConfig)
	{
		//does nothing
	}

	/**
	 * Called by dmModuleSecurityManager
	 *
	 * Check if user has credentials to run the module/action (optionnaly for record)
	 * within its own records permissions or with its own groups
	 *
	 * @param dmUser $user
	 */
	public function userHasCredentials($user)
	{
		$moduleName = $this->action->getModuleName();
		$actionName = $this->action->getActionName();
		$model = $this->action->getDmModule()->getOption('model');
		$record = $this->action->getObject();
		if($record)
		{
			$recordId = $record->get($record->getTable()->getIdentifier());
		}
		$userId = $user->get($user->getTable()->getIdentifier());
		 
		$userRights = dmDb::table('Dmuser')->createQuery('u')->select('u.id')
		->leftJoin('u.Records r')
		->andWhere('u.id = ?', $user->get())
		->andWhere('r.secure_module = ?', $moduleName)
		->andWhere('r.secure_action = ?', $actionName)
		->andWhere('r.secure_model = ?', $model);
		if($record)
		{
			$userRights->andWhere('r.secure_record = ?', $recordId);
		}
		 
		 
		$query = dmDb::table('DmUser')->getRecordsPermissionsForModuleActionModelQuery($moduleName, $actionName, $model, $user);
		if($record){
			$query->andWhere('secure_record = ?', $record->get($record->getTable()->getIdentifier()));
			$query->limit(1);
		}
		$result = $query->execute();
	}

	/**
	 * Called by dmActions->buildQuery()
	 * Restreins the query to what user can do, according to
	 * security: entry in modules.yml
	 *
	 * @param Doctrine_Query $query
	 */
	public function addPermissionCheckToQuery($query)
	{
		$userId = $this->context->getUser()->getUser()->get('id');
		$groupsIds = dmDb::table('DmUser')->getGroupsIds($this->context->getUser()->getUser());
		if(count($groupsIds) === 0){ $groupsIds = array(-1); }
		 
		$groupsRights = dmDb::table('DmRecordPermission')->createQuery('p')->select('p.secure_record')
		->leftJoin('p.Groups g')->whereIn('g.id', $groupsIds)->execute(array(), Doctrine::HYDRATE_SCALAR);
		 
		$groups = array();
		if($groupsRights && !empty($groupsRights))
		{
			foreach($groupsRights as $groupRight){
				$groups[] = $groupRight['p_secure_record'];
			}
		}
		 
		$usersRights = dmDb::table('DmRecordPermission')->createQuery('p')->select('p.secure_record')
		->leftJoin('p.Users u')->where('u.id = ?', $userId)->execute(array(), Doctrine::HYDRATE_SCALAR);

		$users = array();
		if($usersRights && !empty($usersRights)){
			foreach($usersRights as $userRight){
				$users[] = $userRight['p_secure_record'];
			}
		}


		$results = array_unique(array_merge($users, $groups));
		if(empty($results)){$results = array(-1); }
		 
		 
		return $query->andWhereIn($query->getRootAlias() . '.id', $results);
		 
		 
	}

	protected function getUser()
	{
		$this->context->getUser();
	}
}