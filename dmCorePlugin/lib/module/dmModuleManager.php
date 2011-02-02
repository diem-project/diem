<?php

class dmModuleManager
{
  protected
  $types,
  $modules,
  $projectModules,
  $modelModules;
  
  public function load(array $types, array $modules, array $projectModules, array $modelModules)
  {
    $this->types          = $types;
    $this->modules        = $modules;
    $this->projectModules = $projectModules;
    $this->modelModules   = $modelModules;
  }
  
  public function getTypes()
  {
    return $this->types;
  }


  public function getType($typeName)
  {
    return dmArray::get($this->getTypes(), $typeName);
  }

  
  public function getModules()
  {
    return $this->modules;
  }

  public function getProjectModules()
  {
    return $this->projectModules;
  }
  
  public function hasModule($moduleKey)
  {
    return isset($this->modules[$moduleKey]);
  }
  
  /**
   * @param string $moduleKey
   * @param boolean $orNull
   * @throws dmException
   * @return dmModule
   */
  public function getModule($moduleKey, $orNull = false)
  {
    $moduleKey = $moduleKey instanceof dmModule ? $moduleKey->getKey() : $moduleKey;

    if (isset($this->modules[$moduleKey]))
    {
      return $this->modules[$moduleKey];
    }
    elseif (isset($this->modules[$moduleKey = dmString::modulize($moduleKey)]))
    {
      return $this->modules[$moduleKey];
    }

    if ($orNull)
    {
      return null;
    }

    throw new dmException(sprintf('The "%s" module does not exist', $moduleKey));
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
    
    foreach($this->modules as $key => $module)
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
    /*
     * do NOT camelize the model
     */
    if (isset($this->modelModules[$model]))
    {
      return $this->getModule($this->modelModules[$model]);
    }
    else
    {
    	return $this->getModuleByParentModel($model);
    }

    return null;
  }
  
  public function getModuleByParentModel($model)
  {
  		$refl = new ReflectionClass($model);
    	$parent = $refl->getParentClass();
    	if(!in_array($parent->getName(), array('dmDoctrineRecord', 'sfDoctrineRecord', 'Doctrine_Record')))
    	{
    		if(isset($this->modelModules[$parent->getName()]))
    		{
    			return $this->getModule($this->modelModules[$parent->getName()]);
    		}
    		else{
    			return $this->getModuleByParentModel($parent->getName());
    		}
    	}else{
    		return null;
    	}
  }

  public function getModuleBySfName($sfName)
  {
    if ($module = $this->getModuleOrNull($sfName))
    {
      return $module;
    }
    
    foreach($this->getModules() as $module)
    {
      if ($sfName == $module->getSfName())
      {
        return $module;
      }
    }
    
    return null;
  }
  
  public function keysToModules(array $keys)
  {
    $modules = array();
    
    foreach($keys as $key)
    {
      $modules[$key] = $this->getModule($key);
    }
    
    return $modules;
  }
  /**
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
  
  public static function moduleKey($moduleOrKey)
  {
    return $module instanceof dmModule ? $moduleOrKey->getKey() : $moduleOrKey;
  }

}