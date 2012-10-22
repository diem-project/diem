<?php

class dmAdminUser extends dmCoreUser
{
  protected
  $theme,
  $moduleManager,
  $availableModules = array();
  
  public function listenToContextLoadedEvent(sfEvent $e)
  {
    parent::listenToContextLoadedEvent($e);
    
    $this->setModuleManager($e->getSubject()->getModuleManager());
    
    $this->setTheme($e->getSubject()->get('theme'));
  }
  
  public function setModuleManager(dmModuleManager $moduleManager)
  {
    $this->moduleManager = $moduleManager;
  }
  
  /*
   * @return dmTheme the current user theme
   */
  public function getTheme()
  {
    return $this->theme;
  }
  
  public function setTheme(dmTheme $theme)
  {
    $this->theme = $theme;
    
    $this->dispatcher->notify(new sfEvent($this, 'user.change_theme', array('theme' => $theme)));
  }

  public function getAppliedSearchOnModule($module)
  {
    return $this->getAttribute($module.'.search', '', 'admin_module');
  }

  public function getAppliedFiltersOnModule($module)
  {
    $appliedFilters = array();
    foreach($this->getAttribute($module.'.filters', array(), 'admin_module') as $filter => $value )
    {
      if ($value)
      {
        if (is_array($value))
        {
          if (dmArray::get($value, 'text') || dmArray::get($value, 'is_empty') || dmArray::get($value, 'from') || dmArray::get($value, 'to'))
          {
            $appliedFilters[] = $filter;
          }
        }
        else
        {
          $appliedFilters[] = $filter;
        }
      }
    }

    return $appliedFilters;
  }

  public function canAccessToModule($moduleKey)
  {
    if ($moduleKey instanceof dmModule)
    {
      $moduleKey = $moduleKey->getKey();
    }

    if (isset($this->availableModules[$moduleKey]))
    {
      return $this->availableModules[$moduleKey];
    }
    
    if ($moduleKey instanceof dmModule)
    {
      $module = $moduleKey;
    }
    else
    {
      $module = $this->moduleManager->getModule($moduleKey);
    }
    
    return $this->availableModules[$module->getKey()] =
    $module->hasAdmin()
    && $module->getSecurityManager()->userHasCredentials('index');
  }
  
}