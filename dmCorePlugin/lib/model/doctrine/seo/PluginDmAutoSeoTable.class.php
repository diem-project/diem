<?php
/**
 */
class PluginDmAutoSeoTable extends myDoctrineTable
{

	/*
	 * @return DmAutoSeo found or created record
	 */
  public function findOneByModuleAndAction($module, $action)
  {
    return $this->createQuery('a')
    ->where('a.module = ? AND a.action = ?', array($module, $action))
    ->dmCache()
    ->fetchRecord();
  }


  /*
   * @return DmAutoSeo created record
   */
  public function createFromModuleAndAction($module, $action)
  {
    $module = dmModuleManager::getModule($module);

    $moduleUnderscore = $module->getUnderscore();

    return $this->create(array(
      'module'      => $module->getKey(),
      'action'      => $action,
      'slug'        => '%'.$moduleUnderscore.'%',
      'name'        => '%'.$moduleUnderscore.'%',
      'title'       => '%'.$moduleUnderscore.'%',
      'description' => $module->getTable()->hasField('description') ? '%'.$moduleUnderscore.'.description%' : '%'.$moduleUnderscore.'%'
    ));
  }
}