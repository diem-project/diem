<?php

class dmModuleManager
{

  protected static
  $configFile = 'config/dm/modules.yml',
  $types,
  $modules,
  $projectModules;

  public static function getTypes()
  {
    if(null === self::$types)
    {
      self::$types = array();

      $config = include(sfContext::getInstance()->getConfigCache()->checkConfig(self::$configFile));

      foreach($config as $typeName => $spaces)
      {
        self::$types[$typeName] = new dmModuleType($typeName, $spaces);
      }
    }

    return self::$types;
  }

  public static function checkModulesConsistency()
  {
    //$timer = dmDebug::timerOrNull('dmModuleManager::checkModulesConsistency');

    foreach(self::getModules() as $module)
    {
      if (!$module->isProject())
      {
        continue;
      }
      //      if ($parent = $module->getParent())
      //      {
      //        if (!$parent->hasPage())
      //        {
      //          throw new dmException(sprintf(
      //            '%s is child of %s wich has no page. A parent must have a page ( components: [show] )',
      //            $module, $parent
      //          ));
      //        }
      //        if (!$module->hasLocal($parent) && !$module->hasAssociation($parent))
      //        {
      //          throw new dmException(sprintf(
      //            '%s is child of %s but %s is not associated to %s. Add a database referer or many2many class',
      //            $module, $parent, $module->getModel(), $parent->getModel()
      //          ));
      //        }
      //      }
    }

    if (!self::getModuleOrNull('main'))
    {
      throw new dmException('You must create a main module');
    }

    //$timer && $timer->addTime();
  }

  public static function getType($typeName)
  {
    return dmArray::get(self::getTypes(), $typeName);
  }

  public static function getTypeBySlug($slug, $default = null)
  {
    foreach(self::getTypes() as $type)
    {
      if ($type->getSlug() === $slug)
      {
        return $type;
      }
    }
    return $default;
  }

  public static function getModules()
  {
    if (null === self::$modules)
    {
      $timer = dmDebug::timerOrNull('dmModuleManager::getModules');
      self::$modules = array();
      foreach(self::getTypes() as $type)
      {
        foreach($type->getSpaces() as $space)
        {
          self::$modules = array_merge(self::$modules, $space->getModules());
        }
      }
      $timer && $timer->addTime();

      if (sfConfig::get('sf_debug'))
      {
        self::checkModulesConsistency();
      }
    }

    return self::$modules;
  }

  public static function getProjectModules()
  {
    if (null === self::$projectModules)
    {
      self::$projectModules = array();
      foreach(self::getModules() as $moduleKey => $module)
      {
        if ($module->isProject())
        {
          self::$projectModules[$moduleKey] = $module;
        }
      }
    }
    return self::$projectModules;
  }

  public static function getModule($something, $orNull = false)
  {
    if ($something instanceof dmModule)
    {
      return $something;
    }

    $moduleKey = dmString::modulize($something);

    $modules = self::getModules();

    if (isset($modules[$moduleKey]))
    {
      return $modules[$moduleKey];
    }

    if ($orNull)
    {
      return null;
    }

    throw new dmException(sprintf("The %s module does not exist", $something));
  }

  public static function getModuleOrNull($something)
  {
    return self::getModule($something, true);
  }

  public static function getModulesWithPage()
  {
    $modules = self::getProjectModules();

    foreach($modules as $key => $module)
    {
      if (!$module->hasPage())
      {
        unset($modules[$key]);
      }
    }
    return $modules;
  }

  public static function getModulesWithModel()
  {
    $modules = self::getProjectModules();

    foreach($modules as $key => $module)
    {
      if (!$module->hasModel())
      {
        unset($modules[$key]);
      }
    }
    return $modules;
  }

  public static function getModuleByModel($model)
  {
    foreach(self::getProjectModules() as $module)
    {
      if ($module->getModel() == $model)
      {
        return $module;
      }
    }

    foreach(self::getModules() as $module)
    {
      if ($module->getModel() == $model)
      {
        return $module;
      }
    }

    return null;
  }

  /*
   * Remove modules wich are child of another modified module
   * Keep only rooter modified modules
   * @return array of dmModule
   */
  public static function removeModulesChildren(array $modules)
  {
    $unsettedModules = array();
    foreach($modules as $moduleKey => $module)
    {
      foreach($modules as $_moduleKey => $_module)
      {
        if (!isset($unsettedModules[$_moduleKey]) && $_module->hasAncestor($moduleKey))
        {
          $unsettedModules[$_moduleKey] = $_moduleKey;
        }
      }
    }

    foreach(array_unique($unsettedModules) as $unsettedModuleKey)
    {
      unset($modules[$unsettedModuleKey]);
    }

    return $modules;
  }

}