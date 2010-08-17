<?php


class DmUserGroupTable extends PluginDmUserGroupTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmUserGroup');
    }
}