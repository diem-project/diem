<?php
/**
 */
class PluginDmAutoSeoTable extends myDoctrineTable
{
  
  public function findActives($culture = null)
  {
    return $this->createQuery('a')
    ->whereIn('a.module', array_keys(self::$moduleManager->getModulesWithPage()))
    ->withI18n($culture)
    ->dmCache()
    ->fetchRecords();
  }
  
  public function findOneByModuleAndAction($module, $action, $culture = null)
  {
    $culture = null === $culture ? myDoctrineRecord::getDefaultCulture() : $culture;
    
    return $this->createQuery('a')
    ->where('a.module = ?', $module)
    ->andWhere('a.action = ?', $action)
    ->withI18n($culture)
    ->fetchOne();
  }

  /*
   * @return DmAutoSeo created record
   */
  public function createFromModuleAndAction($module, $action, $culture = null)
  {
    $module = $this->getServiceContainer()->getService('module_manager')->getModule($module);

    $moduleUnderscore = $module->getUnderscore();
    
    $identifierColumnName = $module->getTable()->getIdentifierColumnName();
    
    $culture = null === $culture ? myDoctrineRecord::getDefaultCulture() : $culture;
    
    if ('id' == $identifierColumnName)
    {
      $column = '';
    }
    else
    {
      $column = '.'.$identifierColumnName;
    }
    
    $descriptionColumnCandidates = array(
      'excerpt',
      'resume',
      'description',
      'body',
      'text'
    );
    $descriptionColumn = $column;
    
    foreach($descriptionColumnCandidates as $descriptionColumnCandidate)
    {
      if ($module->getTable()->hasField($descriptionColumnCandidate))
      {
        $descriptionColumn = '.'.$descriptionColumnCandidate;
        break;
      }
    }

    return $this->create(array(
      'module'      => $module->getKey(),
      'action'      => $action,
      'Translation' => array(
        $culture    => array(
          'slug'        => '%'.$moduleUnderscore.$column.'%',
          'name'        => '%'.$moduleUnderscore.$column.'%',
          'title'       => '%'.$moduleUnderscore.$column.'%',
          'description' => '%'.$moduleUnderscore.$descriptionColumn.'%',
          'lang'        => $culture
        )
      )
    ));
  }
}