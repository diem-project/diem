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
   * @todo tests, tests, tests
   *
   * @param dmUser $user
   */
  public function userHasCredentials($actionName = null, $record = null)
  {
  	$cacheKey = sprintf('%s/%s/%s/userHasCredential', $this->user->getUser()->get('id'), $this->module->getUnderscore(), $this->action);
    if(null === $record)
    {
      $record = $this->action->getObject();
    }
    if($record && $actionName != 'new' && $this->userHasCredentials('new') && $record && $record->state() != 3) return true;
    if($record && !$this->action->hasPager())
    {
      $args['record'] = $record->get($record->getTable()->getIdentifier());
    }
    if(!$this->has($cacheKey))
    {
      $args = array('module' => $this->action->getModuleName(),
                    'model'  => $this->module->getOption('model')
      );
      $permissionsQuery = dmDb::table('DmUser')->getRecordsPermissionsQuery($args, $this->user->getUser());
      	
      if($this->action->hasPager())
      {
        $records = $this->action->getPager()->getResults();
        $recordsIds = array();
        foreach($records as $r)
        {
          $recordsIds[] = $r->get($r->getTable()->getIdentifier());
        }
        $permissionsQuery->andWhereIn('p.secure_record', $recordsIds);
      }else{

      }
      	
      $permissions = $permissionsQuery->execute(array(), Doctrine::HYDRATE_ARRAY);
      $tmpPerms = array();
      foreach($permissions as $permission)
      {
        $tmpPerms[$permission['secure_record']][$permission['secure_action']] = true;
      }
      $permissions = $tmpPerms;
      unset($tmpPerms, $permission, $permissionsQuery, $args);
      $this->set($cacheKey, $permissions);
    }
    $permissions = $this->getCache($cacheKey);
    if(!$record) return true;
    $recordId = $record->get($record->getTable()->getIdentifier());
    return isset($permissions[$recordId][$actionName]);

  }
  
  public function getCredentials($actionName = null)
  {
  	if($this->userHasCredentials($actionName, $this->action->getObject()))
  	{
  		return true;
  	}
  	return DmPermission::NEVER_GRANT_ACCESS;
  }

  /**
   * Called by dmActions->buildQuery()
   * Restreins the query to objects user can act on.
   *
   * @param Doctrine_Query $query
   */
  public function addPermissionCheckToQuery($query)
  {
    $cacheKey = sprintf('%s/%s/%s/permissionsIds', $this->user->getUser()->get('id'), $this->module->getUnderscore(), $this->action);
    if(!$this->has($cacheKey))
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
      $this->set($cacheKey, $result);
    }
    return $query->andWhereIn($query->getRootAlias() . '.id', $this->get($cacheKey));
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
