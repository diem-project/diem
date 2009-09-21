<?php

class dmModule extends dmMicroCache
{

  protected
    $manager,
    $space,
    $key,
    $params,
    $underscoredKey,
    $checkedModel,
    $table;

  public function __construct(dmModuleManager $manager)
  {
    $this->manager = $manager;
  }
  
  public function initialize($key, dmModuleSpace $space, array $options)
  {
    $this->key    = $key;
    $this->space  = $space;
    
    /*
     * Extract plural from name
     * name | plural
     */
    if (!empty($options['name']))
    {
      if (strpos($options['name'], '|'))
      {
        list($options['name'], $options['plural']) = explode('|', $options['name']);
      }
    }
    else
    {
      $options['name'] = dmString::humanize($key);
    }
    
    $this->params = array(
      'name' =>       $options['name'],
      'plural' =>     empty($options['plural']) ? dmString::pluralize($options['name']) : $options['plural'],
      'model' =>      empty($options['model']) ? dmString::camelize($key) : $options['model'],
      'credentials' => isset($options['credentials']) ? $options['credentials'] : null,
      'options' =>    empty($options['options']) ? $this->getDefaultOptions() : array_merge($this->getDefaultOptions(), sfToolkit::stringToArray($options['options']))
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

    $dirs = dmContext::getInstance()->getConfiguration()->getControllerDirs($this->getKey());

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
      if ($foreignModule = $this->manager->getModuleOrNull($relation->getClass()))
      {
        $foreigns[$foreignModule->getKey()] = $foreignModule;
      }
    }

    return $this->setCache('foreigns', $foreigns);
  }

  public function getForeign($foreignModuleKey)
  {
    if ($foreignModule = $this->manager->getModuleOrNull($foreignModuleKey))
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
    if ($foreignModule = $this->manager->getModuleOrNull($something))
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
      if($localModule = $this->manager->getModuleByModel($relation->getClass()))
      {
        $locals[$localModule->getKey()] = $localModule;
      }
    }

    return $this->setCache('locals', $locals);
  }

  public function getLocal($localModuleKey)
  {
    if ($localModule = $this->manager->getModuleOrNull($localModule))
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
    if ($localModule = $this->manager->getModuleOrNull($something))
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
      $associationModule = $this->manager->getModule($relation->getClass());
      $associations[$associationModule->getKey()] = $associationModule;
    }

    return $this->setCache('associations', $associations);
  }

  public function getAssociation($associationModuleKey)
  {
    if ($associationModule = $this->manager->getModuleOrNull($associationModuleKey))
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
    if ($associationModule = $this->manager->getModule($something))
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
  
  /*
   * @return dmModuleManager
   */
  public function getManager()
  {
    return $this->manager;
  }
}