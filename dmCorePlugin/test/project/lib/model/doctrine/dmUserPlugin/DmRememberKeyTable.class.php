<?php


class DmRememberKeyTable extends PluginDmRememberKeyTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmRememberKey');
    }
}