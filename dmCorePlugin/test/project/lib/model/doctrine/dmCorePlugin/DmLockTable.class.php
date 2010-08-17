<?php


class DmLockTable extends PluginDmLockTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmLock');
    }
}