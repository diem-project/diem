<?php

abstract class PluginDmUserGroup extends BaseDmUserGroup
{
  public function postSave($event)
  {
    parent::postSave($event);

    $this->getUser()->reloadGroupsAndPermissions();
  }
}