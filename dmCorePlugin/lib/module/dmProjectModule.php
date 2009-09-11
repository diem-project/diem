<?php

class dmProjectModule extends dmModule
{

  protected
    $actions = array();

  public function __construct($key, $config, dmModuleSpace $space)
  {
    parent::__construct($key, $config, $space);

    $this->params['actions'] = array();
    foreach(dmArray::get($config, "actions", array()) as $actionKey => $actionConfig)
    {
    	if (is_integer($actionKey))
    	{
        $actionKey = $actionConfig;
    		$action = new dmAction($actionKey, array());
    	}
    	else
    	{
    		$action = new dmAction($actionKey, $actionConfig);
    	}
      $this->params['actions'][$actionKey] = $action;
    }

    $this->params['parentKey'] = dmArray::get($config, 'parent');
    
    $this->params['hasPage'] = dmArray::get($config, 'page', false);
  }

  public function hasPage()
  {
  	return $this->params['hasPage'];
  }

  // ACCESSEURS

  public function hasModel()
  {
    return parent::hasModel() && in_array($this->getModel(), dmProject::getModels());
  }

  public function getActions()
  {
    return $this->getParam("actions");
  }

  public function getAction($actionKey)
  {
    return dmArray::get($this->getActions(), $actionKey);
  }

  public function hasAction($actionKey)
  {
    return array_key_exists($actionKey, $this->getActions());
  }


  public function getParent()
  {
    if ($this->hasCache('parent'))
    {
    	return $this->getCache('parent');
    }

    return $this->setCache('parent', dmModuleManager::getModuleOrNull($this->getParam('parentKey')));
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
    	return dmModuleManager::getModule($descendantKey);
    }
    
    return null;
  }
  
  public function hasDescendant($descendantKey)
  {
    return dmModuleManager::getModule($descendantKey)->hasAncestor($this->getKey());
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
    $fromModule = dmModuleManager::getModule($fromModule);
    
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
    if ($this->hasCache('children'))
    {
      return $this->getCache('children');
    }
    $children = array();
    foreach(dmModuleManager::getProjectModules() as $otherModule)
    {
      if ($otherModule->getParam('parentKey') === $this->key)
      {
      	$children[$otherModule->getKey()] = $otherModule;
      }
    }
    return $this->setCache('children', $children);
  }

  public function hasChildren()
  {
    return count($this->getChildren()) !== 0;
  }

  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      "auto" => true
    ));
  }

}