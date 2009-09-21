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
    if ($this->hasCache('parent'))
    {
      return $this->getCache('parent');
    }

    return $this->setCache('parent', $this->manager->getModuleOrNull($this->getParentKey()));
  }
  
  public function getParentKey()
  {
    return $this->options['parent_key'];
  }

  public function hasParent()
  {
    return null !== $this->getParent();
  }

  public function getAncestor($ancestorKey)
  {
    if ($ancestorKey instanceof dmModule)
    {
      $ancestorKey = $ancestorKey->getKey();
    }
    
    return dmArray::get($this->getPath(), dmString::modulize($ancestorKey));
  }

  public function hasAncestor($ancestorKey)
  {
    if ($ancestorKey instanceof dmModule)
    {
      $ancestorKey = $ancestorKey->getKey();
    }
    
    return array_key_exists(dmString::modulize($ancestorKey), $this->getPath());
  }

  public function knows($module)
  {
    return $this->is($module) || $this->hasAncestor($module);
  }

  public function getFarthestAncestor()
  {
    return dmArray::first($this->getPath());
  }

  public function getFarthestAncestorWithPage()
  {
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
    return $this->manager->getModule($descendantKey)->hasAncestor($this->getKey());
  }

  /*
   * get all the ancestor modules, from farthest to nearest
   * @return array an array of moduleKey => dmModule
   */
  public function getPath($includeMe = false)
  {
    if ($this->hasCache('path'))
    {
      $path = $this->getCache('path');
    }
    else
    {
      $path = array();

      $ancestorModule = $this;
      while($ancestorModule = $ancestorModule->getParent())
      {
        $path[$ancestorModule->getKey()] = $ancestorModule;
      }

      $path = array_reverse($path, true);

      $this->setCache('path', $path);
    }

    if ($includeMe)
    {
      $path[$this->getKey()] = $this;
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
    $children = array();
    
    foreach($this->options['children_keys'] as $childKey)
    {
      $children[$childKey] = $this->manager->getModule($childKey);
    }
    
    return $children;
  }

  public function hasChildren()
  {
    return !empty($this->options['children_keys']);
  }

  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      "auto" => true
    ));
  }

}