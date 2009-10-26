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
    $module = self::$moduleManager->getModule($module);

    $moduleUnderscore = $module->getUnderscore();
    
    $identifierColumnName = $module->getTable()->getIdentifierColumnName();
    
    if ('id' == $identifierColumnName)
    {
      $column = '';
    }
    else
    {
      $column = '.'.$identifierColumnName;
    }

    return $this->create(array(
      'module'      => $module->getKey(),
      'action'      => $action,
      'slug'        => '%'.$moduleUnderscore.$column.'%',
      'name'        => '%'.$moduleUnderscore.$column.'%',
      'title'       => '%'.$moduleUnderscore.$column.'%',
      'description' => $module->getTable()->hasField('description') ? '%'.$moduleUnderscore.'.description%' : '%'.$moduleUnderscore.$column.'%'
    ));
  }
}