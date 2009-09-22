<?php

class dmProjectModule extends dmModule
{
  public function hasPage()
  {
    return $this->options['has_page'];
  }

  // ACCESSEURS

  public function getActions()
  {
    return $this->options['actions'];
  }

  public function getAction($actionKey)
  {
    return isset($this->options['actions'][$actionKey]) ? $this->options['actions'][$actionKey] : null;
  }

  public function hasAction($actionKey)
  {
    return isset($this->options['actions'][$actionKey]);
  }

  public function getParent()
  {
    return $this->manager->getModule($this->getParentKey());
  }
  
  public function getParentKey()
  {
    return $this->options['parent_key'];
  }

  public function hasParent()
  {
    return null !== $this->options['parent_key'];
  }

  public function getAncestor($ancestorKey)
  {
    if (!$this->hasParent())
    {
      return null;
    }
    
    if ($ancestorKey instanceof dmModule)
    {
      $ancestorKey = $ancestorKey->getKey();
    }
    else
    {
      $ancestorKey = dmString::modulize($ancestorKey);
    }
    
    return in_array($ancestorKey, $this->options['path_keys']) ? $this->manager->getModule($ancestorKey) : null;
  }

  public function hasAncestor($ancestorKey)
  {
    if (!$this->hasParent())
    {
      return null;
    }
    
    if ($ancestorKey instanceof dmModule)
    {
      $ancestorKey = $ancestorKey->getKey();
    }
    
    return in_array(dmString::modulize($ancestorKey), $this->options['path_keys']);
  }

  public function knows($module)
  {
    return $this->is($module) || $this->hasAncestor($module);
  }

  public function getFarthestAncestor()
  {
    if (!$this->hasParent())
    {
      return null;
    }

    return $this->manager->getModule($this->options['path_keys'][0]);
  }

  public function getFarthestAncestorWithPage()
  {
    if (!$this->hasParent())
    {
      return null;
    }
    
    foreach($this->getPath() as $module)
    {
      if ($module->hasPage())
      {
        return $module;
      }
    }

    return null;
  }

  public function getNearestAncestorWithPage()
  {
    if (!$this->hasParent())
    {
      return null;
    }
    
    foreach(array_reverse($this->getPath()) as $module)
    {
      if ($module->hasPage())
      {
        return $module;
      }
    }

    return null;
  }

  public function getDescendant($descendantKey)
  {
    if($this->hasDescendant($descendantKey))
    {
      return $this->manager->getModule($descendantKey);
    }
    
    return null;
  }
  
  public function hasDescendant($descendantKey)
  {
    return $this->manager->getModule($descendantKey)->hasAncestor($this->key);
  }

  /*
   * get all the ancestor modules, from farthest to nearest
   * @return array an array of moduleKey => dmModule
   */
  public function getPath($includeMe = false)
  {
    $path = $this->manager->keysToModules($this->options['path_keys']);

    if ($includeMe)
    {
      $path[$this->key] = $this;
    }

    return $path;
  }
  
  /*
   * get ancestor modules, from farthest to nearest, starting to $fromModule
   * @return array an array of moduleKey => dmModule
   */
  public function getPathFrom($fromModule, $includeMe = false)
  {
    $fromModule = $this->manager->getModule($fromModule);
    
    $path = $this->getPath($includeMe);
    
    if (!array_key_exists($fromModule->getKey(), $path))
    {
      throw new dmException(sprintf('Can not get %s module path from %s because it is not a valid ancestor', $this, $fromModule));
    }
    
    foreach($path as $ancestorKey => $ancestor)
    {
      if ($ancestor->is($fromModule))
      {
        break;
      }
      else
      {
        unset($path[$ancestorKey]);
      }
    }
    
    return $path;
  }

  public function hasListPage()
  {
    return !$this->hasParent() && $this->hasModel();
  }

  public function getChildren()
  {
    return $this->manager->keysToModules($this->options['children_keys']);
  }

  public function hasChildren()
  {
    return !empty($this->options['children_keys']);
  }

  
  public function hasDirectAction($action)
  {
    return in_array($action, $this->options['direct_actions']);
  }
  
  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      "auto" => true
    ));
  }

}