<?php


class DmZoneTable extends PluginDmZoneTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmZone');
    }
}