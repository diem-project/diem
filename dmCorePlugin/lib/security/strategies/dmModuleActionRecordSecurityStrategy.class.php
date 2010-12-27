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