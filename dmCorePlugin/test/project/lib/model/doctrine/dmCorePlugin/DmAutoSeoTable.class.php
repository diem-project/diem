<?php


class DmAutoSeoTable extends PluginDmAutoSeoTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmAutoSeo');
    }
}