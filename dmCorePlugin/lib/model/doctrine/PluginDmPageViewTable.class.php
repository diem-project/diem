<?php
/**
 */
class PluginDmPageViewTable extends myDoctrineTable
{

  public function findOneByModuleAndAction($module, $action)
  {
    return $this->createQuery('p')
    ->where('p.module = ? AND p.action = ?', array($module, $action))
    ->fetchRecord();
  }

  /**
   * @return DmPageView created record
   */
  public function createFromModuleAndAction($module, $action)
  {
    return dmDb::create('DmPageView', array(
      'module' => $module,
      'action' => $action,
      'dm_layout_id' => dmDb::table('DmLayout')->findFirstOrCreate()
    ))->saveGet();
  }

}