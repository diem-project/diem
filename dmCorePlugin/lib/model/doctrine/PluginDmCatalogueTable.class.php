<?php
/**
 */
class PluginDmCatalogueTable extends myDoctrineTable
{

  public function retrieveBySourceTargetSpace($source, $target, $space)
  {
    return $this->createQuery('c')
    ->where('c.source_lang = ?', $source)
    ->andWhere('c.target_lang = ?', $target)
    ->andWhere('c.name = ?', $space.'.'.$target)
    ->fetchRecord();
  }
}