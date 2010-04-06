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
}
