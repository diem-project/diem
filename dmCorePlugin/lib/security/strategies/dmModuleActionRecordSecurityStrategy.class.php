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
  public function secure(dmModule $module, $actionName, $actionConfig)
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
  public function userHasCredentials($actionName = null, $record = null)
  {
    $cacheKey = 'userHasCredential_' . (null === $actionName ? $this->action->getActionName() : $actionName);
    $cacheKey .= null === $record ? '' : '_' . $record->get($record->getTable()->getIdentifier());
    if(!$this->hasCache($cacheKey))
    {
      $args = array('module' => $this->action->getModuleName(), 
                    'action' => null === $actionName ? $this->action->getActionName() : $actionName,
                    'model'  => $this->module->getOption('model')
      );
      if(null === $record)
      {
      	$record = $this->action->getObject();
      }
      if($record)
      {
        $args['record'] = $record->get($record->getTable()->getIdentifier());
      }
      $permissions = dmDb::table('DmUser')->hasRecordsPermission($args, $this->user->getUser());
      return $this->setCache($cacheKey, !empty($permissions));
    }
    return $this->getCache($cacheKey);
  }

  /**
   * Called by dmActions->buildQuery()
   * Restreins the query to objects user can act on.
   *
   * @param Doctrine_Query $query
   */
  public function addPermissionCheckToQuery($query)
  {
    $cacheKey = 'permissionsIds';
    if(!$this->hasCache($cacheKey))
    {
      $result = array();
      $queryResult = dmDb::table('DmUser')->getModelPermissions($this->action, $this->user->getUser());
      if(empty($queryResult))
      { 
        $result[] = -1; 
      }else{
        foreach($queryResult as $permission)
        {
          $result[] = $permission['secure_record'];
        }
      }
      $this->setCache($cacheKey, $result);
    }
    return $query->andWhereIn($query->getRootAlias() . '.id', $this->getCache($cacheKey));
  }
  
  public function getIdsForAuthorizedActionWithinIds($actionName, $ids)
  {
  	$userId = $this->user->getUser()->get($this->user->getUser()->getTable()->getIdentifier());
  	
  	$query = dmDb::table('DmRecordPermission')->createQuery('p')->select('p.secure_record')
  	->leftJoin('p.Users u')
  	->leftJoin('p.Groups g')
  	->leftJoin('g.Users u1')
  	->andWhere('(u.id = ? OR u1.id = ?)', array($userId, $userId))
  	->andWhere('p.secure_model = ?', $this->module->getOption('model'))
  	->andWhere('p.secure_action = ?', $actionName)
  	->andWhereIn('p.secure_record', $ids);
  	
  	$permissions = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
  	
  	if(empty($permissions)) return false;
  	
  	$authorized = array();
  	foreach($permissions as $permission)
  	{
  		$authorized[] = $permission['secure_record'];
  	}
  	return $authorized;
  }
}