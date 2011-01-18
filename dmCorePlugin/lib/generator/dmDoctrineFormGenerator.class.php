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

			if(!$useDmForm = $this->moduleManager->getModuleByModel($model))
			{
				/// find column_aggregation inheritance superclass
				foreach((array) $this->table->getOption('subclasses') as $subClass)
				{
					if($this->moduleManager->getModuleByModel($subClass))
					{
						$useDmForm = true;
						break;
					}
				}
				/// find concrete inheritance superclass
				if(!$useDmForm)
				{
					foreach($this->moduleManager->getModulesWithModel() as $module)
					{
						$r = new ReflectionClass($module->getModel());
						if ($r->isSubclassOf($model))
						{
							$useDmForm = true;
							break;
						}
					}
				}
			}

			$this->setGeneratorClass($useDmForm ? 'dmDoctrineForm' : 'sfDoctrineForm');

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
				}
				else
				{
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
	 * @return dmDoctrineTable
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * @return array
	 */
	public function getMediaRelations()
	{
		return $this->table->getRelationHolder()->getLocalMedias();
	}

	/**
	 * Returns a sfWidgetForm class name for a given column.
	 *
	 * @param  sfDoctrineColumn $column
	 * @return string    The name of a subclass of sfWidgetForm
	 */
	public function getWidgetClassForColumn($column)
	{
		if($column instanceof sfDoctrineColumn)
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
		}

		if ($column instanceof sfDoctrineColumn && $column->isPrimaryKey())
		{
			$widgetSubclass = 'InputHidden';
		}
		else if ($column instanceof sfDoctrineColumn &&  $column->isForeignKey() || $column instanceof Doctrine_Relation_LocalKey)
		{
		  if($this->table->isPaginatedColumn($column instanceof sfDoctrineColumn ? $column->getName() : $column['local']))
		  {
		    $widgetSubclass = 'DmPaginatedDoctrineChoice';
		  }else{
			  $widgetSubclass = 'DmDoctrineChoice';
		  }
		}

		//listeners are expecting an sfDoctrineColumn instance
		//thanks to Yoann BRIEUX
		if($column instanceof Doctrine_Relation_LocalKey)
		{
			$column = new sfDoctrineColumn($column['local'], $this->table);
		}
		$widgetSubclass = $this->getGeneratorManager()->getConfiguration()->getEventDispatcher()->filter(
		new sfEvent($this, 'dm.form_generator.widget_subclass', array('column' => $column)),
		$widgetSubclass
		)->getReturnValue();
		
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

		if ($column->isPrimaryKey())
		{
			$validatorSubclass = 'Choice';
		}
		elseif ($column->isForeignKey())
		{
			$validatorSubclass = 'DoctrineChoice';
		}

		$validatorClass = isset($validatorClass) ? $validatorClass : sprintf('sfValidator%s', $validatorSubclass);

		$validatorClass = $this->getGeneratorManager()->getConfiguration()->getEventDispatcher()->filter(
		new sfEvent($this, 'dm.form_generator.validator_class', array('column' => $column)),
		$validatorClass
		)->getReturnValue();

		return $validatorClass;
	}

	/**
	 * Get array of sfDoctrineColumn objects that exist on the current model but not its parent.
	 *
	 * @param boolean $withoutColumnAggregationKeys To include or not column_aggregation keys (columns) set by subclasses
	 * @return array $columns
	 */
	public function getColumns($withoutColumnAggregationKeys = false, $withoutLocalKeyRelationsFromSubclasses = false, $withoutLocalKeys = false)
	{
		$parentModel = $this->getParentModel();
		$parentColumns = $parentModel ? array_keys(Doctrine_Core::getTable($parentModel)->getColumns()) : array();

		$columns = array();
		$selfColumns = array_diff(array_keys($this->table->getColumns()), $parentColumns);

		if($withoutColumnAggregationKeys)
		{
			$subClasses = (array) $this->getTable()->getOption('subclasses');
			foreach($subClasses as $subClass)
			{
				$subTableInheritanceMap = dmDb::table($subClass)->getOption('inheritanceMap');
				if(!empty($subTableInheritanceMap))
				{
					$selfColumns = array_diff($selfColumns, array_keys($subTableInheritanceMap));
				}
			}
		}

		if($withoutLocalKeyRelationsFromSubclasses)
		{
			$subClasses = (array) $this->getTable()->getOption('subclasses');
			$relations = $this->table->getRelations();
			foreach($subClasses as $subClass)
			{
				$subClassRelations = dmDb::table($subClass)->getRelations();
				$subClassLocalKeysRelations = array();
				foreach($subClassRelations as $subClassRelation)
				{
					if($subClassRelation instanceof Doctrine_Relation_LocalKey)
					{
						$subClassLocalKeysRelations[] = $subClassRelation['local'];
					}
				}
				$selfColumns = array_diff($selfColumns, $subClassLocalKeysRelations);
			}
		}
		
		if($withoutLocalKeys)
		{
		  $relations = $this->table->getRelations();
		  $toRemove = array();
		  foreach($relations as $relation)
		  {
		    if($relation instanceof Doctrine_Relation_LocalKey)
		    {
		      $toRemove[] = $relation['local'];
		    }
		  }
		  $selfColumns = array_diff($selfColumns, $toRemove);
		}

		foreach ($selfColumns as $name)
		{
			$columns[] = new dmDoctrineColumn($name, $this->table);
		}

		return $columns;
	}

	/**
	 * Returns an array containing the columns declared because of
	 * subclasses inheriting using column_aggregation and declaring
	 * a column to discriminate the record's class
	 *
	 * @return array
	 */
	public function getColumnAggregationKeyFields()
	{
		$columnAggregationKeyColumns = array();
		$subClasses = $this->getTable()->getOption('subclasses');
		if(!empty($subClasses))
		{
			foreach($subClasses as $subClass)
			{
				$subTableInheritanceMap = dmDb::table($subClass)->getOption('inheritanceMap');
				if(!empty($subTableInheritanceMap))
				{
					$columnName = array_keys($subTableInheritanceMap);
					$columnName = $columnName[0];
					$columnAggregationKeyColumns[$columnName] = new dmDoctrineColumn($columnName, $this->table);
				}
			}
		}
		return $columnAggregationKeyColumns;
	}

	/**
	 * Returns an array containing the subclasses inheriting using column_aggregate,
	 * using their declared discriminant.
	 *
	 * @return array the choices used by sfWidgetFormChoice and sfValidatorChoice
	 */
	public function getSubClassesChoices()
	{
		$choices = array();
		$subClasses = $this->getTable()->getOption('subclasses');
		if(!empty($subClasses))
		{
			foreach($subClasses as $subClass)
			{
				$subTableInheritanceMap = dmDb::table($subClass)->getOption('inheritanceMap');
				if(!empty($subTableInheritanceMap))
				{
					$columnName = array_keys($subTableInheritanceMap);
					$columnName = $columnName[0];
					$discriminant = $subTableInheritanceMap[$columnName];
					$choices[$discriminant] = ucfirst(dmString::humanize($discriminant));
				}
			}
		}
		return $choices;
	}

	public function getSubClassesChoicesValidator()
	{
		$choices = array();
		$subClasses = $this->getTable()->getOption('subclasses');
		if(!empty($subClasses))
		{
			foreach($subClasses as $subClass)
			{
				$subTableInheritanceMap = dmDb::table($subClass)->getOption('inheritanceMap');
				if(!empty($subTableInheritanceMap))
				{
					$columnName = array_keys($subTableInheritanceMap);
					$columnName = $columnName[0];
					$discriminant = $subTableInheritanceMap[$columnName];
					$choices[] = $discriminant;
				}
			}
		}
		return $choices;
	}

	/**
	 * Returns an array of relations representing a many
	 */
	public function getOneToManyRelations()
	{
		$relations = array();
		foreach ($this->table->getRelations() as $relation)
		{
			if (
			$relation instanceof Doctrine_Relation_ForeignKey
			&&
			Doctrine_Relation::MANY == $relation->getType()
			&&
			(null === $this->getParentModel() || !Doctrine_Core::getTable($this->getParentModel())->hasRelation($relation->getAlias()))
			&&
			$this->getOneToManyOppositeRelation($relation)
			)
			{
				$relations[] = $relation;
			}
		}

		return $relations;
	}

	public function getOneToOneRelations()
	{
		$relations = array();
		foreach ($this->table->getRelations() as $relation)
		{
			if (
			$relation instanceof Doctrine_Relation_LocalKey
			&&
			Doctrine_Relation::ONE == $relation->getType()
			&&
			(null === $this->getParentModel() || !Doctrine_Core::getTable($this->getParentModel())->hasRelation($relation->getAlias()))
			)
			{
				$relations[] = $relation;
			}
		}

		return $relations;
	}

	public function getOneToManyOppositeRelation($relation)
	{
		$relation = is_string($relation) ? $this->table->getRelation($relationName) : $relation;
		$relatedTable = $relation->getTable();
		$local = $relation['id'];
		$foreign = $relation['foreign'];

		foreach($relatedTable->getRelations() as $relatedTableRelation)
		{
			if($relatedTableRelation['local'] === $foreign)
			{
				return $relatedTableRelation;
			}
		}
		return false;
	}

	public function getNumberOfSpaces($text)
	{
		$nb = $this->getColumnNameMaxLength() - strlen($text);
		if( $nb < 0) return 0;
		return $nb;
	}

	/**
	 * Returns a PHP string representing options to pass to a widget for a given column.
	 *
	 * @param sfDoctrineColumn $column
	 *
	 * @return string The options to pass to the widget as a PHP string
	 */
	public function getWidgetOptionsForColumn($column)
	{
		$options = array();

		if ($column->isForeignKey())
		{
			$options[] = sprintf('\'model\' => $this->getRelatedModelName(\'%s\'), \'add_empty\' => %s', $column->getRelationKey('alias'), $column->isNotNull() ? 'false' : 'true');
		}
		else if ('enum' == $column->getDoctrineType() && is_subclass_of($this->getWidgetClassForColumn($column), 'sfWidgetFormChoiceBase'))
		{
			$options[] = '\'choices\' => '.$this->arrayExport(array_combine($column['values'], $column['values']));
		}

		return count($options) ? sprintf('array(%s)', implode(', ', $options)) : '';
	}
}
