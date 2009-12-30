<?php

abstract class PluginDmUserPermission extends BaseDmUserPermission
{
  public function postSave($event)
  {
    parent::postSave($event);

    $this->getUser()->reloadGroupsAndPermissions();
  }
}