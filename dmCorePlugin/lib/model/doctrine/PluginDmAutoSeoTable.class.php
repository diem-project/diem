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

  /*
   * @return DmAutoSeo created record
   */
  public function createFromModuleAndAction($module, $action, $culture = null)
  {
    $module = self::$moduleManager->getModule($module);

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
      'body'
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