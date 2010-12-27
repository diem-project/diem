<?php

abstract class PluginDmUserTable extends myDoctrineTable
{
  /**
   * Retrieves a DmUser object from his username and is_active flag.
   *
   * @param string $username The username
   * @param boolean $isActive The user's status
   * @return DmUser
   */
  public function retrieveByUsername($username, $isActive = true)
  {
    return $this->createQuery('u')
    ->where('u.username = ?', $username)
    ->addWhere('u.is_active = ?', $isActive)
    ->fetchRecord();
  }

  /**
   * Retrieves a DmUser object from his forgot password code
   *
   * @param string $code The forgot password code
   * @param boolean $isActive The user's status
   * @return DmUser
   */
  public function retrieveByForgotPasswordCode($code, $isActive = true)
  {
    return $this->createQuery('u')
    ->where('u.forgot_password_code = ?', $code)
    ->addWhere('u.is_active = ?', $isActive)
    ->fetchRecord();
  }

  /**
   * Retrieves a DmUser object from his email and is_active flag.
   *
   * @param string $email The email
   * @param boolean $isActive The user's status
   * @return DmUser
   */
  public function retrieveByEmail($email, $isActive = true)
  {
    return $this->createQuery('u')
    ->where('u.email = ?', $email)
    ->addWhere('u.is_active = ?', $isActive)
    ->fetchRecord();
  }
  
  public function findOneById($id)
  {
    return $this->createQuery('u')->where('u.id = ?', $id)->fetchRecord();
  }
  
  public function getAdminListQuery(dmDoctrineQuery $query)
  {
    return $this->joinDmMedias($query);
  }
  
  public function getHumanColumns()
  {
    $columns = parent::getHumanColumns();
    
    unset($columns['algorithm'], $columns['salt'], $columns['password']);
    
    return $columns;
  }

  public function getIdentifierColumnName()
  {
    return 'username';
  }
  
  /**
   * 
   * 
   * Adds for security
   * 
   * 
   */
  
  public function getRecordsPermissionsForModuleActionModelQuery($module, $action, $model, $user, $permissionsAlias = 'p', $userAlias = 'u')
	{
		return $this->getBaseRecordPermissionQuery($user, $permissionsAlias, $userAlias)
		->andWhere('p.secure_module = ?', $module)
		->andWhere('p.secure_action = ?', $action)
		->andWhere('p.secure_model = ?', $model)
		->select('u.name, g.name');
	}
	
	public function getBaseRecordPermissionQuery($user, $permissionsAlias = 'r', $userAlias = 'u', $groupsAlias = 'g')
	{
		return dmDb::table('DmRecordPermission')->createQuery($permissionsAlias)
		->leftJoin('p.Groups ' . $groupsAlias)
			->whereIn($groupsAlias .'.id', $this->getGroupsIds($user))
	  ->leftJoin('p.Users ' . $userAlias)
			->andWhere($userAlias. '.id = ?', $user->get($this->getIdentifier()))
		->leftJoin($userAlias .'.Groups ug');
	}
	
	public function getGroupsIds($user)
	{
		$groupsResult = dmDb::table('DmGroup')->createQuery('g')
		->leftJoin('g.Users u')
		->where('u.id = ?', $user->get($this->getIdentifier()))
		->execute();
		
		$groups = array();
		foreach($groupsResult as $group)
		{
			$groups[] = $group->get('id');
		}
		return $groups;
	}
	
  
}
