<?php


class DmCatalogueTable extends PluginDmCatalogueTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmCatalogue');
    }
}