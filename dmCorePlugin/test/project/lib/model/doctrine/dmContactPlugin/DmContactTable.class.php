<?php


class DmContactTable extends PluginDmContactTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmContact');
    }
}