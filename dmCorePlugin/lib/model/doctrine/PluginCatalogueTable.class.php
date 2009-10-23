<?php
/**
 */
class PluginCatalogueTable extends myDoctrineTable
{

  public function retrieveBySourceTargetSpace($source, $target, $space)
  {
    return $this->createQuery('c')
    ->where('c.source_lang = ? AND c.target_lang = ? AND c.name = ?', array($source, $target, $space.'.'.$target))
    ->fetchRecord();
  }
}