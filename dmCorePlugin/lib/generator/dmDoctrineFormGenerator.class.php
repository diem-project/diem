<?php


class dmDoctrineFormGenerator extends sfDoctrineFormGenerator
{
  protected
  $moduleManager;
  
  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);
    
    if (!dmContext::hasInstance())
    {
      dmContext::createInstance($generatorManager->getConfiguration());
    }
    
    $this->moduleManager = dmContext::getInstance()->getModuleManager();
    
    $this->setGeneratorClass('dmDoctrineForm');
  }

  public function generate($params = array())
  {
    $this->generateBaseClasses();
    
    $this->params = $params;

    if (!isset($this->params['model_dir_name']))
    {
      $this->params['model_dir_name'] = 'model';
    }

    if (!isset($this->params['form_dir_name']))
    {
      $this->params['form_dir_name'] = 'form';
    }

    $models = $this->loadModels();

    $pluginPaths = $this->generatorManager->getConfiguration()->getAllPluginPaths();

    // create a form class for every Doctrine class
    foreach ($models as $model)
    {
      $this->table = Doctrine_Core::getTable($model);
      $this->modelName = $model;

      if ($this->moduleManager->getModuleByModel($model))
      {
        $this->setGeneratorClass('dmDoctrineForm');
      }
      else
      {
        $this->setGeneratorClass('sfDoctrineForm');
      }
      
      $baseDir = sfConfig::get('sf_lib_dir') . '/form/doctrine';

      $isPluginModel = $this->isPluginModel($model);
      
      if ($isPluginModel)
      {
        $pluginName = $this->getPluginNameForModel($model);
        $baseDir .= '/' . $pluginName;
      }

      if (!is_dir($baseDir.'/base'))
      {
        mkdir($baseDir.'/base', 0777, true);
      }

      file_put_contents($baseDir.'/base/Base'.$model.'Form.class.php', $this->evalTemplate(null === $this->getParentModel() ? 'sfDoctrineFormGeneratedTemplate.php' : 'sfDoctrineFormGeneratedInheritanceTemplate.php'));

      if ($isPluginModel)
      {
        $pluginBaseDir = $pluginPaths[$pluginName].'/lib/form/doctrine';
        if (!file_exists($classFile = $pluginBaseDir.'/Plugin'.$model.'Form.class.php'))
        {
            if (!is_dir($pluginBaseDir))
            {
              mkdir($pluginBaseDir, 0777, true);
            }
            file_put_contents($classFile, $this->evalTemplate('sfDoctrineFormPluginTemplate.php'));
        }
      }
      if (!file_exists($classFile = $baseDir.'/'.$model.'Form.class.php'))
      {
        if ($isPluginModel)
        {
           file_put_contents($classFile, $this->evalTemplate('sfDoctrinePluginFormTemplate.php'));
        } else {
           file_put_contents($classFile, $this->evalTemplate('sfDoctrineFormTemplate.php'));
        }
      }
    }
  }
  
  protected function generateBaseClasses()
  {
    // create the project base class for all forms
    $file = sfConfig::get('sf_lib_dir').'/form/BaseForm.class.php';
    if (!file_exists($file))
    {
      if (!is_dir($directory = dirname($file)))
      {
        mkdir($directory, 0777, true);
      }

      copy(dmOs::join(sfConfig::get('dm_core_dir'), 'data/skeleton/lib/form/BaseForm.class.php'), $file);
    }

    // create the project base class for all doctrine forms
    $file = sfConfig::get('sf_lib_dir').'/form/doctrine/BaseFormDoctrine.class.php';
    if (!file_exists($file))
    {
      if (!is_dir($directory = dirname($file)))
      {
        mkdir($directory, 0777, true);
      }

      copy(dmOs::join(sfConfig::get('dm_core_dir'), 'data/skeleton/lib/form/doctrine/BaseFormDoctrine.class.php'), $file);
    }
  }

  /**
   * Filter out models that have disabled generation of form classes
   *
   * @return array $models Array of models to generate forms for
   */
  protected function filterModels($models)
  {
    $models = parent::filterModels($models);
    
    foreach ($models as $key => $model)
    {
      if (strncmp($model, 'ToPrfx', 6) === 0)
      {
        unset($models[$key]);
      }
    }

    return $models;
  }

  public function getModule()
  {
    return $this->moduleManager->getModuleByModel($this->table->getComponentName());
  }

  public function getMediaRelations()
  {
    return $this->getModule()->getTable()->getRelationHolder()->getLocalMedias();
  }

  /**
   * Returns a sfWidgetForm class name for a given column.
   *
   * @param  sfDoctrineColumn $column
   * @return string    The name of a subclass of sfWidgetForm
   */
  public function getWidgetClassForColumn($column)
  {
    switch ($column->getDoctrineType())
    {
      case 'string':
        $widgetSubclass = null === $column->getLength() || $column->getLength() > 255 ? 'Textarea' : 'InputText';
        break;
      case 'boolean':
        $widgetSubclass = 'InputCheckbox';
        break;
      case 'blob':
      case 'clob':
        $widgetSubclass = 'Textarea';
        break;
      case 'date':
        $widgetSubclass = 'DmDate';
        break;
      case 'time':
        $widgetSubclass = 'Time';
        break;
      case 'timestamp':
        $widgetSubclass = 'DateTime';
        break;
      case 'enum':
        $widgetSubclass = 'Choice';
        break;
      default:
        $widgetSubclass = 'InputText';
    }

    if ($column->isPrimaryKey())
    {
      $widgetSubclass = 'InputHidden';
    }
    else if ($column->isForeignKey())
    {
      $widgetSubclass = 'DoctrineChoice';
    }

    return sprintf('sfWidgetForm%s', $widgetSubclass);
  }

  /**
   * Returns a sfValidator class name for a given column.
   *
   * @param sfDoctrineColumn $column
   * @return string    The name of a subclass of sfValidator
   */
  public function getValidatorClassForColumn($column)
  {
    switch ($column->getDoctrineType())
    {
      case 'boolean':
        $validatorSubclass = 'Boolean';
        break;
      case 'string':
        if ($column->getDefinitionKey('email'))
        {
          $validatorSubclass = 'Email';
        }
        elseif ($column->getDefinitionKey('regexp'))
        {
          $validatorSubclass = 'Regex';
        }
        elseif ($column->getTable()->isLinkColumn($column->getName()))
        {
          $validatorClass = 'dmValidatorLinkUrl';
        }
        else
        {
          $validatorSubclass = 'String';
        }
        break;
      case 'clob':
      case 'blob':
        $validatorSubclass = 'String';
        break;
      case 'float':
      case 'decimal':
        $validatorSubclass = 'Number';
        break;
      case 'integer':
        $validatorSubclass = 'Integer';
        break;
      case 'date':
        $validatorClass = 'dmValidatorDate';
        break;
      case 'time':
        $validatorSubclass = 'Time';
        break;
      case 'timestamp':
        $validatorSubclass = 'DateTime';
        break;
      case 'enum':
        $validatorSubclass = 'Choice';
        break;
      default:
        $validatorSubclass = 'Pass';
    }

    if ($column->isPrimaryKey() || $column->isForeignKey())
    {
      $validatorSubclass = 'DoctrineChoice';
    }

    return isset($validatorClass) ? $validatorClass : sprintf('sfValidator%s', $validatorSubclass);
  }

}