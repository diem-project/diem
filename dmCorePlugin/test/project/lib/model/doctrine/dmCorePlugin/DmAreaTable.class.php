<?php


class DmAreaTable extends PluginDmAreaTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmArea');
    }
}