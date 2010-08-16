<?php


class DmTransUnitTable extends PluginDmTransUnitTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmTransUnit');
    }
}