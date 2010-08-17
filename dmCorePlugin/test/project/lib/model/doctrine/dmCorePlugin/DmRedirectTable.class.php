<?php


class DmRedirectTable extends PluginDmRedirectTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmRedirect');
    }
}