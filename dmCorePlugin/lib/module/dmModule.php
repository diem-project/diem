<?php

class dmModule extends dmMicroCache
{

  protected
    $space,
    $key,
    $params,
    $underscoredKey,
    $checkedModel,
    $table;

  public function __construct($key, $config = array(), dmModuleSpace $space)
  {
    $this->key   = $key;
    $this->space = $space;

    /*
     * Extract plural from name
     * name | plural
     */
    if (isset($config['name']))
    {
      if (strpos($config['name'], '|'))
      {
        list($config['name'], $config['plural']) = explode('|', $config['name']);
      }
    }
    
    $name   = empty($config['name']) ? dmString::humanize($key) : trim($config['name']);
    
    $this->params = array(
      'name' =>       $name,
      'plural' =>     empty($config['plural']) ? dmString::pluralize($name) : $config['plural'],
      'model' =>      empty($config['model']) ? dmString::camelize($key) : $config['model'],
      'credentials' => isset($config['credentials']) ? $config['credentials'] : null,
      'options' =>    empty($config['options']) ? $this->getDefaultOptions() : array_merge($this->getDefaultOptions(), sfToolkit::stringToArray($config['options']))
    );
  }

  public function getSpace()
  {
    return $this->space;
  }

  public function isInternal()
  {
  	return !$this->isProject() && strncmp($this->getKey(), 'dm', 2) === 0;
  }

  public function isProject()
  {
  	return $this->getSpace()->getType()->isProject();
  }

  public function getOptions()
  {
    return $this->getParam("options");
  }

  public function getOption($option_key, $default = null)
  {
    return dmArray::get($this->getOptions(), $option_key, $default);
  }

  public function hasAdmin()
  {
    return $this->getOption('admin');
  }

  public function __toString()
  {
    return $this->getKey();
  }

  public function toDebug()
  {
  	return $this->toArray();
  }

  public function getKey()
  {
    return $this->key;
  }

  public function getParam($key)
  {
    return isset($this->params[$key]) ? $this->params[$key] : null;
  }

  public function setParam($key, $value)
  {
    return $this->params[$key] = $value;
  }

  public function getName()
  {
    return $this->getParam("name");
  }

  public function getPlural()
  {
    return $this->getParam("plural");
  }
  
  public function getCredentials()
  {
    return $this->getCredentials();
  }

  public function getModel()
  {
  	if (null === $this->checkedModel)
  	{
  	  $this->checkedModel = false;
	    if ($model = $this->getParam("model"))
	    {
		    if(Doctrine::isValidModelClass($model))
		    {
			    $this->checkedModel = $model;
		    }
	    }
  	}

  	return $this->checkedModel;
  }

  public function hasModel()
  {
  	return $this->getModel() !== false;
  }

  public function hasPage()
  {
  	return false;
  }

  public function getUnderscore()
  {
    if (null === $this->underscoredKey)
    {
  	  $this->underscoredKey = dmString::underscore($this->getKey());
    }
    return $this->underscoredKey;
  }

  public function getSlug()
  {
  	if ($this->hasCache('slug'))
  	{
  		return $this->getCache('slug');
  	}

  	return $this->setCache('slug', $this->slug = dmString::slugify(dm::getI18n()->__($this->getPlural())));
  }

  public function getCompleteSlug()
  {
  	if($this->hasCache('complete_slug'))
  	{
  		return $this->getCache('complete_slug');
  	}

  	return $this->setCache('complete_slug',
  	  implode('/', array(
	      $this->getSpace()->getType()->getSlug(),
	      $this->getSpace()->getSlug(),
	      $this->getSlug()
	    ))
	  );
  }


  /*
   * Full system path to the symfony module directory
   * @return string|null /path/to/the/module
   */
  public function getDir()
  {
  	if($this->hasCache('dir'))
  	{
  		return $this->getCache('dir');
  	}

  	$dirs = sfContext::getInstance()->getConfiguration()->getControllerDirs($this->getKey());

  	$dir = null;
  	foreach($dirs as $actionPath => $isProject)
  	{
  		if(file_exists($actionPath))
  		{
        $dir = preg_replace('|^(.+)/actions$|', '$1', $actionPath);
  			break;
  		}
  	}

  	return $this->setCache('dir', $dir);
  }

  public function getTable()
  {
  	if (null === $this->table)
  	{
  		$model = $this->getModel();
  		$this->table = $model ? dmDb::table($this->getModel()) : false;
  	}

    return $this->table;
  }

  public function getForeigns()
  {
    if ($this->hasCache('foreigns'))
    {
      return $this->getCache('foreigns');
    }

    $foreigns = array();
    foreach($this->getTable()->getRelationHolder()->getForeigns() as $relation)
    {
      if ($foreignModule = dmModuleManager::getModuleOrNull($relation->getClass()))
      {
        $foreigns[$foreignModule->getKey()] = $foreignModule;
      }
    }

    return $this->setCache('foreigns', $foreigns);
  }

  public function getForeign($foreignModuleKey)
  {
    if ($foreignModule = dmModuleManager::getModuleOrNull($foreignModuleKey))
    {
      if ($this->hasForeign($foreignModule))
      {
        return $foreignModule;
      }
    }
    return null;
  }

  public function hasForeign($something)
  {
    if ($foreignModule = dmModuleManager::getModuleOrNull($something))
    {
      return array_key_exists($foreignModule->getKey(), $this->getForeigns());
    }
    return false;
  }

  public function getLocals()
  {
    if ($this->hasCache('locals'))
    {
      return $this->getCache('locals');
    }

    $locals = array();
    foreach($this->getTable()->getRelationHolder()->getLocals() as $relation)
    {
      if($localModule = dmModuleManager::getModuleByModel($relation->getClass()))
      {
        $locals[$localModule->getKey()] = $localModule;
      }
    }

    return $this->setCache('locals', $locals);
  }

  public function getLocal($localModuleKey)
  {
    if ($localModule = dmModuleManager::getModuleOrNull($localModule))
    {
      if ($this->hasLocal($localModule))
      {
        return $localModule;
      }
    }
    return null;
  }

  public function hasLocal($something)
  {
    if ($localModule = dmModuleManager::getModuleOrNull($something))
    {
      return array_key_exists($localModule->getKey(), $this->getLocals());
    }
    return false;
  }

  public function getAssociations()
  {
  	if ($this->hasCache('associations'))
  	{
  		return $this->getCache('associations');
  	}

  	$associations = array();
    foreach($this->getTable()->getRelationHolder()->getAssociations() as $key => $relation)
    {
      $associationModule = dmModuleManager::getModule($relation->getClass());
      $associations[$associationModule->getKey()] = $associationModule;
    }

    return $this->setCache('associations', $associations);
  }

  public function getAssociation($associationModuleKey)
  {
    if ($associationModule = dmModuleManager::getModuleOrNull($associationModuleKey))
    {
      if ($this->hasAssociation($associationModule))
      {
        return $associationModule;
      }
    }
    return null;
  }

  public function hasAssociation($something)
  {
    if ($associationModule = dmModuleManager::getModule($something))
    {
      return array_key_exists($associationModule->getKey(), $this->getAssociations());
    }
    return false;
  }

  public function getDefaultOptions()
  {
  	return array(
      'admin' => true,
  	  'page'  => false
    );
  }

  public function toArray()
  {
    return array(
      'key' => $this->getKey(),
      'model' => $this->getModel(),
      'params' => $this->params
    );
  }

  public function is($something)
  {
  	if (is_string($something))
  	{
  		return $this->getKey() == $something;
  	}

  	return $this == $something;
  }
  
  public function interactsWithPageTree()
  {
  	return $this->isProject();
  }
}