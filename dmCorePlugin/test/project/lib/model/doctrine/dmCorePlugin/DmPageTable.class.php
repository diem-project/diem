<?php


class DmPageTable extends PluginDmPageTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmPage');
    }
}