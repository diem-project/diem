<?php

class dmDoctrineFormFilterGenerator extends sfDoctrineFormFilterGenerator
{
	/**
	 * Initializes the current sfGenerator instance.
	 *
	 * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
	 */
	public function initialize(sfGeneratorManager $generatorManager)
	{
		parent::initialize($generatorManager);

		$this->setGeneratorClass('dmDoctrineFormFilter');
	}

	/**
	 * Generates classes and templates in cache.
	 *
	 * @param array $params The parameters
	 *
	 * @return string The data to put in configuration cache
	 */
	public function generate($params = array())
	{
		// create the project base class for all forms
		$file = sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php';
		if (!file_exists($file))
		{
			if (!is_dir($directory = dirname($file)))
			{
				mkdir($directory, 0777, true);
			}

			copy(dmOs::join(sfConfig::get('dm_core_dir'), 'data/skeleton/lib/filter/doctrine/BaseFormFilterDoctrine.class.php'), $file);
		}

		parent::generate($params);
	}

	public function getWidgetOptionsForColumn($column)
	{
		$options = array();

		$withEmpty = sprintf('\'with_empty\' => %s', $column->isNotNull() ? 'false' : 'true');
		switch ($column->getDoctrineType())
		{
			case 'boolean':
				$options[] = "'choices' => array('' => \$this->getI18n()->__('yes or no', array(), 'dm'), 1 => \$this->getI18n()->__('yes', array(), 'dm'), 0 => \$this->getI18n()->__('no', array(), 'dm'))";
				break;
			case 'date':
			case 'datetime':
			case 'timestamp':
				$options[] = "'choices' => array(
        ''      => '',
        'today' => \$this->getI18n()->__('Today'),
        'week'  => \$this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => \$this->getI18n()->__('This month'),
        'year'  => \$this->getI18n()->__('This year')
      )";
				break;
			case 'enum':
				$values = array('' => '');
				$values = array_merge($values, $column['values']);
				$values = array_combine($values, $values);
				$options[] = "'multiple' => true, 'choices' => " . str_replace("\n", '', $this->arrayExport($values));
				break;
		}

		if ($column->isForeignKey() || $column instanceof Doctrine_Relation_LocalKey)
		{
			$options[] = sprintf('\'multiple\' => true, \'model\' => \'%s\', \'add_empty\' => true', $column->getForeignTable()->getOption('name'));
		}

		return count($options) ? sprintf('array(%s)', implode(', ', $options)) : '';
	}

	/**
	 * Returns a PHP string representing options to pass to a validator for a given column.
	 *
	 * @param  sfDoctrineColumn $column
	 * @return string    The options to pass to the validator as a PHP string
	 */
	public function getValidatorOptionsForColumn($column)
	{
		$options = parent::getValidatorOptionsForColumn($column);
      switch ($column->getDoctrineType())
      {
        case 'boolean':
			 $options = "array('required' => false, 'choices' => array(0, 1))";
          break;
        case 'date':
        case 'datetime':
        case 'timestamp':
			 $options = "array('required' => false, 'choices' => array_keys(\$this->widgetSchema['{$column->getName()}']->getOption('choices')))";
          break;
        case 'enum':
			 $options = array("'required' => false");
          $options[] = "'multiple' => true ";
          $values = array_combine($column['values'], $column['values']);
          $options[] = "'choices' => ".$this->arrayExport($values);
			 $options = sprintf('array(%s)', implode(', ', $options));
          break;
      }
		return $options;
	}

	public function getValidatorForColumn( $column )
	{
		$format = 'new %s(%s)';

		if (in_array( $class = $this->getValidatorClassForColumn( $column ) , array( 'sfValidatorInteger' , 'sfValidatorNumber' , 'sfValidatorString'  ) ))
		{
			$format = 'new sfValidatorSchemaFilter(\'text\', new %s(%s))';
		}

		return sprintf( $format , $class , $this->getValidatorOptionsForColumn( $column ) );
	}

	public function getAllColumns()
	{
		return array_merge($this->getColumns(), $this->getI18nColumns());
	}

	/**
	 * @return array sfDoctrineColumn
	 */
	protected function getI18nColumns()
	{
		$columns = array();

		if($this->isI18n())
		{
			$i18nTable = $this->table->getI18nTable();

			foreach(array_keys($i18nTable->getColumns()) as $name)
			{
				$columns[] = new sfDoctrineColumn($name, $i18nTable);
			}
		}

		return $columns;
	}

	/**
	 * Returns the maximum length for a column name.
	 *
	 * @return integer The length of the longer column name
	 */
	public function getColumnNameMaxLength()
	{
		$max = parent::getColumnNameMaxLength();

		foreach ($this->getI18nColumns() as $column)
		{
			if (($m = strlen($column->getFieldName())) > $max)
			{
				$max = $m;
			}
		}

		return $max;
	}

	/**
	 *
	 * @todo refactorize these methods with the ones in dmDoctrineFormGenerator
	 * 				a Helper class has been made, use it !
	 *
	 */


	public function getTable()
	{
		return dmDb::table($this->modelName);
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
	 * Returns an array of relations representing a many
	 */
	public function getOneToManyRelations()
	{
		$relations = array();
		foreach ($this->getTable()->getRelations() as $relation)
		{
			if (
			$relation instanceof Doctrine_Relation_ForeignKey
			&&
			Doctrine_Relation::MANY == $relation->getType()
			&&
			(null === $this->getParentModel() || !dmDb::table($this->getParentModel())->hasRelation($relation->getAlias()))
			&&
			$this->getOneToManyOppositeRelation($relation)
			)
			{
				$relations[] = $relation;
			}
		}

		return $relations;
	}

	public function getOneToManyOppositeRelation($relation)
	{
		$relation = is_string($relation) ? $this->getTable()->getRelation($relationName) : $relation;
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

	public function getOneToOneRelations()
	{
		$relations = array();
		foreach ($this->getTable()->getRelations() as $relation)
		{
			if (
			$relation instanceof Doctrine_Relation_LocalKey
			&&
			Doctrine_Relation::ONE == $relation->getType()
			&&
			(null === $this->getParentModel() || !dmDb::table($this->getParentModel())->hasRelation($relation->getAlias()))
			)
			{
				$relations[] = $relation;
			}
		}

		return $relations;
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
			$class = parent::getWidgetClassForColumn($column);

			if('sfWidgetFormFilterDate' == $class)
			{
				$class = 'sfWidgetFormChoice';
			}
			elseif('sfWidgetFormFilterInput' == $class)
			{
				$class = 'sfWidgetFormDmFilterInput';
			}
		}

		if ($column instanceof sfDoctrineColumn &&  $column->isForeignKey() || $column instanceof Doctrine_Relation_LocalKey)
		{
			if($this->getTable()->isPaginatedColumn($column instanceof sfDoctrineColumn ? $column->getName() : $column['local']))
			{
				$class = 'sfWidgetFormDmDoctrineChoice';
			}else{
				$class = 'sfWidgetFormDoctrineChoice';
			}
		}

		$class = $this->getGeneratorManager()->getConfiguration()->getEventDispatcher()->filter(
		new sfEvent($this, 'dm.form_filter_generator.widget_class', array('column' => $column)),
		$class
		)->getReturnValue();

		return $class;
	}

	/**
	 * Returns a sfValidator class name for a given column.
	 *
	 * @param sfDoctrineColumn $column
	 * @return string    The name of a subclass of sfValidator
	 */
	public function getValidatorClassForColumn($column)
	{
		$class = parent::getValidatorClassForColumn($column);

		if('sfValidatorDateRange' == $class)
		{
			return 'sfValidatorChoice';
		}

		switch ($column->getDoctrineType())
		{
			case 'boolean':
				$validatorSubclass = 'Choice';
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
				if(!$column->isPrimaryKey()){
					$validatorSubclass = 'Integer';
				}
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

		$validatorClass = isset($validatorClass) ? $validatorClass : sprintf('sfValidator%s', $validatorSubclass);

		$validatorClass = $this->getGeneratorManager()->getConfiguration()->getEventDispatcher()->filter(
		new sfEvent($this, 'dm.form_generator.validator_class', array('column' => $column)),
		$validatorClass
		)->getReturnValue();

		return $validatorClass;
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

}
