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
  
  public function findOneById($id)
  {
    return $this->createQuery('u')->where('u.id = ', $id)->dmCache()->fetchRecord();
  }
  
  public function getAdminListQuery(dmDoctrineQuery $query)
  {
    return $query;
  }
  
  public function getHumanColumns()
  {
    $columns = parent::getHumanColumns();
    
    unset($columns['algorithm'], $columns['salt'], $columns['password']);
    
    return $columns;
  }
}
