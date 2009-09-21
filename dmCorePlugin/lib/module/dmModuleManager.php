<?php

class dmModuleManager
{

  protected
  $types,
  $modules,
  $projectModules;
  
  public function __construct(array $options = array())
  {
    $this->initialize($options);
  }
  
  public function initialize(array $options = array())
  {
    $this->options = $options;
    
    $this->load();
  }
  
  protected function load()
  {
    $this->types   = array();
    $this->modules = array();
    $this->projectModules = array();

    $config = include(sfContext::getInstance()->getConfigCache()->checkConfig('config/dm/modules.yml'));
    
    foreach($config as $typeName => $spacesConfig)
    {
      $type = new $this->options['type_class'];
      $typeSpaces = array();

      $moduleClass = $this->options['Project' === $typeName ? 'module_node_class' : 'module_base_class'];
      
      foreach($spacesConfig as $spaceName => $modulesConfig)
      {
        $space = new $this->options['space_class'];
        $spaceModules = array();
        
        foreach($modulesConfig as $moduleKey => $moduleConfig)
        {
          $moduleKey = dmString::modulize($moduleKey);
          
          $module = new $moduleClass($this);
          
          $module->initialize($moduleKey, $space, $moduleConfig);
          
          $spaceModules[$moduleKey] = $module;
          
          /*
           * Cache modules and project modules for fast access
           */
          $this->modules[$moduleKey] = $module;
          
          if('Project' === $typeName)
          {
            $this->projectModules[$moduleKey] = $module;
          }
        }
      
        $space->initialize($spaceName, $type, $spaceModules);
        $typeSpaces[$spaceName] = $space;
        // unset($spaceModules);
      }
      
      $type->configure($typeName, $typeSpaces);
      // unset($typeSpaces);
      
      $this->types[$typeName] = $type;
    }
    
    unset($config);
  }

  public function getTypes()
  {
    return $this->types;
  }

  public function checkModulesConsistency()
  {
    if (!$this->getModuleOrNull('main'))
    {
      throw new dmException('You must have a main module');
    }
  }

  public function getType($typeName)
  {
    return dmArray::get($this->getTypes(), $typeName);
  }

  public function getTypeBySlug($slug, $default = null)
  {
    foreach($this->getTypes() as $type)
    {
      if ($type->getSlug() === $slug)
      {
        return $type;
      }
    }
    
    return $default;
  }
  
  public function getModules()
  {
    return $this->modules;
  }

  public function getProjectModules()
  {
    return $this->projectModules;
  }

  public function getModule($something, $orNull = false)
  {
    if ($something instanceof dmModule)
    {
      return $something;
    }

    $moduleKey = dmString::modulize($something);

    if (isset($this->modules[$moduleKey]))
    {
      return $this->modules[$moduleKey];
    }

    if ($orNull)
    {
      return null;
    }

    throw new dmException(sprintf("The %s module does not exist", $something));
  }

  public function getModuleOrNull($something)
  {
    return $this->getModule($something, true);
  }

  public function getModulesWithPage()
  {
    $modulesWithPage = array();
    
    foreach($this->projectModules as $key => $module)
    {
      if ($module->hasPage())
      {
        $modulesWithPage[$key] = $module;
      }
    }
    
    return $modulesWithPage;
  }

  public function getModulesWithModel()
  {
    $modulesWithModel = array();
    
    foreach($this->projectModules as $key => $module)
    {
      if ($module->hasModel())
      {
        $modulesWithModel[$key] = $module;
      }
    }
    
    return $modulesWithModel;
  }

  public function getModuleByModel($model)
  {
    foreach($this->getProjectModules() as $module)
    {
      if ($module->getModel() == $model)
      {
        return $module;
      }
    }

    foreach($this->getModules() as $module)
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