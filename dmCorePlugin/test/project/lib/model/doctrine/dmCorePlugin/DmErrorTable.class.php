<?php


class DmErrorTable extends PluginDmErrorTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmError');
    }
}