<?php

class PluginDmUserTable extends myDoctrineTable
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
    return Doctrine::getTable('DmUser')->createQuery('u')
            ->where('u.username = ?', $username)
            ->addWhere('u.is_active = ?', $isActive)
            ->fetchOne();
  }
}
