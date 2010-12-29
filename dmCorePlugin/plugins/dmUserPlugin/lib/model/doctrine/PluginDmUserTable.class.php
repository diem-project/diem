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
   * Let you retrive all records permissions of the user, through its Records relation,
   * or through its Groups.Records relation.
   * 
   * The $args array is the one required by $this->getRecordsPermissionsQuery();
   *
   * @param array $args
   * @param dmUser $user
   * @param Doctrine::HYDRATE_* $hydrationMode
   */
  public function getRecordsPermissions($args, $user, $hydrationMode = Doctrine::HYDRATE_ARRAY)
  {
    $cacheKey = 'recordsPermissions_' . serialize($args) . '_' . $hydrationMode;
    if(!$this->hasCache($cacheKey))
    {
      return $this->setCache($cacheKey, $this->getRecordsPermissionsQuery($args, $user)->execute(array(), $hydrationMode));
    }
    return $this->getCache($cacheKey);
  }
  
  /**
   * Returns if $user can acts on $module || $action || $model || $record
   * 
   * @todo cache it
   * @param array $args
   * @param unknown_type $user
   */
  public function hasRecordsPermission($args, $user)
  {
    return $this->getRecordsPermissionsQuery($args, $user)->limit(1)->execute(array(), Doctrine::HYDRATE_NONE);
  }

  /**
   * The $args array let you specify constraints. You can specify the module, the action, the model
   * or the record's pk.
   *
   * The recognized args are "module", "action", "model" and "record".
   * 
   * @param array $args
   * @param DmUser $user
   */
  public function getRecordsPermissionsQuery($args, $user)
  {
    $query = dmDb::table('DmRecordPermission')->createQuery('p')
    ->select('p.id, p.secure_module, p.secure_action, p.secure_model, p.secure_record')
    ->leftJoin('p.Groups g')
    ->leftJoin('p.Users u')
    ->leftJoin('g.Users u1')
    ->addWhere('(u.id = ? OR u1.id = ?)', array($user->get($this->getIdentifier()), $user->get($this->getIdentifier())));

    if(isset($args['module']))
    {
      $moduleName = $args['module'] instanceof DmModule ? $args['module']->getSfName() : $args['module'];
      $query->andWhere('p.secure_module = ?', $moduleName);
    }

    if(isset($args['action']))
    {
      $actionName = $args['action'] instanceof dmBaseActions ? $args['action']->getActionName() : $args['action'];
      $query->andWhere('p.secure_action = ?', $actionName);
    }

    if(isset($args['model']))
    {
      $modelName = is_string($args['model']) ? $args['model'] : ($args['model'] instanceof dmDoctrineRecord ? $args['model']->getComponentName() : null);
      if($modelName)
      {
        $query->andWhere('p.secure_model = ?', $modelName);
      }
    }

    if(isset($args['record']))
    {
      $recordPk = is_string($args['record']) || is_int($args['record']) ? $args['record'] : null;
      if($recordPk)
      {
        $query->andWhere('p.secure_record = ?', $recordPk);
      }
    }
    return $query;
  }

  /**
   * Returns the permissions of a dmUser for given $action and $user.
   *
   * @param Doctrine_Query $query
   * @param dmBaseActions $action
   * @param DmUser $user
   * @param Doctrine::HYDRATION_* $hydrationMode
   */
  public function getModelPermissions(dmBaseActions $action, $user, $hydrationMode = Doctrine::HYDRATE_ARRAY)
  {
    $cacheKey = sprintf('recordPermissionsQuery_%s_%s_%d_%s', $action->getModuleName(), $action->getActionName(), $user->get($this->getIdentifier()), $hydrationMode);
    if(!$this->hasCache($cacheKey))
    {
      return $this->setCache($cacheKey, $this->getRecordsPermissions(array('module'=>$action->getModuleName(), 'action'=>$action->getActionName(), 'model'=>$action->getDmModule()->getOption('model')), $user, $hydrationMode));
    }
    return $this->getCache($cacheKey);
  }
  
  /**
   * This method returns an array indexed by record id, containing authorized actions
   * for given module and user.
   * 
   * When using this method, make sure you specify the $args['model'] && $args['module'] !
   * 
   * @param unknown_type $args
   * @param DmUser $user
   */
  public function getRecordsPermissionsByRecord($args, DmUser $user, $limit = 0)
  {
    $cacheKey = sprintf('recordsPermissionsByRecord_%s_%s', serialize($args), $user->get($user->getTable()->getIdentifier()));
    if(!isset($args['model'])){ throw new LogicException('You must specify the $args[\'model\'] key !'); }
    if(!isset($args['module'])){ throw new LogicException('You must specify the $args[\'module\'] key !'); }
    $records = array();
    $permissions = $this->getRecordsPermissionsQuery($args, $user)->limit($limit)->execute(array(), Doctrine::HYDRATE_ARRAY);
    if(is_array($permissions) && !empty($permissions))
    {
      foreach($permissions as $permission)
      {
        if(!isset($records[$permission['secure_record']])) { $records[$permission['secure_record']] = array(); }
        $records[$permission['secure_record']][] = $permission['secure_action'];
      }
    }
    return $records;
  }
}
