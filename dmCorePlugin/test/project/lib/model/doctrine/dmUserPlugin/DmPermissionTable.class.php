<?php


class DmPermissionTable extends PluginDmPermissionTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmPermission');
    }
}