<?php


class DmSettingTable extends PluginDmSettingTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmSetting');
    }
}