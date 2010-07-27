<?php


class DmGroupPermissionTable extends PluginDmGroupPermissionTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmGroupPermission');
    }
}