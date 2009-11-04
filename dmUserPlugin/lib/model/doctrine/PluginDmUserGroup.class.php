<?php

abstract class PluginDmUserGroup extends BaseDmUserGroup
{
  /**
   * Saves the current DmUserGroup object in database.
   *
   * @param Doctrine_Connection $conn A Doctrine_Connection object
   */
  public function save(Doctrine_Connection $conn = null)
  {
    parent::save($conn);

    $this->getUser()->reloadGroupsAndPermissions();
  }
}