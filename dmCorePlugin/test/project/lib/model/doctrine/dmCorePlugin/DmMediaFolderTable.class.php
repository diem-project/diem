<?php


class DmMediaFolderTable extends PluginDmMediaFolderTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmMediaFolder');
    }
}