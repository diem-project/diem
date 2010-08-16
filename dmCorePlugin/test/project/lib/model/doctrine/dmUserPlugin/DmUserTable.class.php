<?php


class DmUserTable extends PluginDmUserTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmUser');
    }
}