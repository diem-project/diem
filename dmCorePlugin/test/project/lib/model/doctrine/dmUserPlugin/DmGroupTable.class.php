<?php


class DmGroupTable extends PluginDmGroupTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmGroup');
    }
}