<?php


class DmTagTable extends PluginDmTagTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmTag');
    }
}