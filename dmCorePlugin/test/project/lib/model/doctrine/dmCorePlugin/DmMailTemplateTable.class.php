<?php


class DmMailTemplateTable extends PluginDmMailTemplateTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmMailTemplate');
    }
}