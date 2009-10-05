<?php

class dmAdminUser extends dmUser
{
  protected
  $theme,
  $availableModules = array();

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
          if (dmArray::get($value, 'text') || dmArray::get($value, 'is_empty'))
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
      $module = dmContext::getInstance()->getModuleManager()->getModule($moduleKey);
    }
    
    return $this->availableModules[$module->getKey()] =
    $module->hasAdmin()
    && ($module->isProject() || in_array($moduleKey, sfConfig::get('sf_enabled_modules')))
    && (!$module->getParam('credentials') || $this->can($module->getParam('credentials')));
  }
  
}