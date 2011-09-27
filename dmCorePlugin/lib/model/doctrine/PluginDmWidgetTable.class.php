<?php
/**
 */
class PluginDmWidgetTable extends myDoctrineTable
{

  public function createInZone(DmZone $zone, $moduleAction, $values = array())
  {
    list($module, $action) = explode('/', $moduleAction);

    $widget = $this->create(array(
      'dm_zone_id' => $zone->get('id'),
      'module' => $module,
      'action' => $action
    ));

    $widget->setValues($values);

    return $widget;
  }

  public function findOneByIdWithI18n($id, $culture = null)
  {
    return $this->createQuery('w')
    ->where('w.id = ?', $id)
    ->withI18n($culture, null, 'w')
    ->fetchOne();
  }
}