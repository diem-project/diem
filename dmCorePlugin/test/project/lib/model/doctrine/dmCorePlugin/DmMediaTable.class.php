<?php


class DmMediaTable extends PluginDmMediaTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmMedia');
    }
}