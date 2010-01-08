<?php
/**
 */
class PluginDmWidgetTable extends myDoctrineTable
{

  public function findOneByIdWithI18n($id, $culture = null)
  {
    return $this->createQuery('w')
    ->where('w.id = ?', $id)
    ->withI18n($culture, null, 'w')
    ->fetchOne();
  }
}