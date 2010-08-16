<?php


class DmWidgetTable extends PluginDmWidgetTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmWidget');
    }
}