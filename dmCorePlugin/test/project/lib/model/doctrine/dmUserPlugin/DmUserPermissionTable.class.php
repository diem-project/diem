<?php


class DmUserPermissionTable extends PluginDmUserPermissionTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmUserPermission');
    }
}