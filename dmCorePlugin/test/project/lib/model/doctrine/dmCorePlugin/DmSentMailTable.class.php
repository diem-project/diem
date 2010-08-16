<?php


class DmSentMailTable extends PluginDmSentMailTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DmSentMail');
    }
}