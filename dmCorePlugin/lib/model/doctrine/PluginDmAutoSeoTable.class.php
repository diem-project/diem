<?php
/**
 */
class PluginDmAutoSeoTable extends myDoctrineTable
{
  
  public function findActives($culture = null)
  {
    return $this->createQuery('a')
    ->whereIn('a.module', array_keys($this->getModuleManager()->getModulesWithPage()))
    ->withI18n($culture)
    ->fetchRecords();
  }
  
  public function findOneByModuleAndAction($module, $action, $culture = null)
  {
    return $this->createQuery('a')
    ->where('a.module = ?', $module)
    ->andWhere('a.action = ?', $action)
    ->withI18n($culture)
    ->fetchOne();
  }

  /**
   * @return DmAutoSeo created record
   */
  public function createFromModuleAndAction($moduleKey, $action, $culture = null)
  {
    $module = $this->getModuleManager()->getModule($moduleKey);

    $patterns = $this->getDefaultPatterns($module, $action, $culture);

    return $this->create(array(
      'module'      => $module->getKey(),
      'action'      => $action,
      'Translation' => array(
        $culture    => array(
          'slug'        => $patterns['short'],
          'name'        => $patterns['short'],
          'title'       => $patterns['short'],
          'description' => $patterns['long'],
          'lang'        => $culture
        )
      )
    ));
  }

  protected function getDefaultPatterns(dmModule $module, $action)
  {
    if('show' !== $action)
    {
      return array(
        'short' => $action,
        'long'  => $action
      );
    }

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

    return array(
      'short' => '%'.$moduleUnderscore.$column.'%',
      'long'  => '%'.$moduleUnderscore.$descriptionColumn.'%'
    );
  }
}