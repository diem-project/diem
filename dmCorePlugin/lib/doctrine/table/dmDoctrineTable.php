<?php

abstract class dmDoctrineTable extends Doctrine_Table
{
	protected static
	$eventDispatcher,   // mandatory
	$moduleManager,     // mandatory
	$serviceContainer;  // optional

	protected
	$hasI18n;           // cache

	public function construct()
	{
		$this->hasI18n = $this->hasRelation('Translation');
	}

	/**
	 * @return DmMediaFolder the DmMediaFolder used to store this table's record's medias
	 */
	public function getDmMediaFolder()
	{
		if ($this->hasCache('dm_media_folder'))
		{
			return $this->getCache('dm_media_folder');
		}

		return $this->setCache('dm_media_folder', dmDb::table('DmMediaFolder')->findOneByRelPathOrCreate($this->getDmModule()->getUnderscore()));
	}
	/**
	 * @return bool if this table's records interact with page tree
	 * so if a record is saved or deleted, page tree must be updated
	 */
	public function interactsWithPageTree()
	{
		if($this->hasCache('interacts_with_page_tree'))
		{
			return $this->getCache('interacts_with_page_tree');
		}
		/*
		 * If table belongs to a project module,
		 * it may interact with tree
		 */
		if ($module = $this->getDmModule())
		{
			$interacts = $module->interactsWithPageTree();
		}
		/*
		 * If table owns project records,
		 * it may interact with tree
		 */
		else
		{
			$interacts = false;
			foreach($this->getRelationHolder()->getLocals() as $localRelation)
			{
				if ($localModule = $this->getModuleManager()->getModuleByModel($localRelation['class']))
				{
					if ($localModule->interactsWithPageTree())
					{
						$interacts = true;
						break;
					}
				}
			}
		}
		 
		return $this->setCache('interacts_with_page_tree', $interacts);
	}

	/**
	 * @return myDoctrineRecord the first record in the table
	 */
	public function findOne()
	{
		return $this->createQuery()->fetchRecord();
	}

	/**
	 * Will join all record available medias
	 * @return myDoctrineQuery
	 */
	public function joinDmMedias(myDoctrineQuery $query)
	{
		foreach($this->getRelationHolder()->getLocalMedias() as $relation)
		{
			$query->withDmMedia($relation->getAlias());
		}

		return $query;
	}

	/**
	 * Will join all localKey relations
	 * @param myDoctrineQuery $query the query to modify
	 * @param boolean $withI18n join Translation ?
	 * @param array $relations array of relations alias; if empty add all relations found
	 * @return dmDoctrineTable
	 */
	public function joinLocals(myDoctrineQuery $query, $withI18n = false, $relations = array())
	{
		$rootAlias = $query->getRootAlias();

		foreach($this->getRelationHolder()->getLocals() as $relation)
		{
			if(!empty($relations) && !in_array($relation['alias'], $relations)) continue;
			if ($relation->getClass() === 'DmMedia')
			{
				$query->withDmMedia($relation->getAlias());
			}
			else
			{
				$joinAlias = dmString::lcfirst($relation->getAlias());
				$query->leftJoin(sprintf('%s.%s %s', $rootAlias, $relation->getAlias(), $joinAlias));

				if($withI18n && $relation->getTable()->hasI18n())
				{
					$joinTranslationAlias = $joinAlias.'Translation';
					$query->leftJoin($joinAlias.'.Translation '.$joinTranslationAlias.' ON '.$joinAlias.'.id = '.$joinTranslationAlias.'.id AND '.$joinTranslationAlias.'.lang = ?', myDoctrineRecord::getDefaultCulture());
				}
			}
		}

		return $this;
	}

	public function fetchJoinAll($params = array(), $hydrationMode = Doctrine_Core::HYDRATE_RECORD)
	{
		return $this->joinAll()->execute($params, $hydrationMode);
	}

	/**
	 * Will join all relations
	 * @return myDoctrineQuery
	 */
	public function joinAll(dmDoctrineQuery $query = null)
	{
		if ($query instanceof dmDoctrineQuery)
		{
			$rootAlias = $query->getRootAlias();
		}
		else
		{
			$query = $this->createQuery($rootAlias = 'q');
		}

		foreach($this->getRelationHolder()->getAll() as $relation)
		{
			if($relation->getAlias() === 'Version' && $this->isVersionable())
			{
				continue;
			}
			elseif ($relation->getAlias() === 'Translation')
			{
				$query->withI18n();
			}
			elseif ($relation->getClass() === 'DmMedia')
			{
				if ($relation instanceof Doctrine_Relation_Association && $this->hasTemplate('DmGallery'))
				{
					continue;
				}

				$query->withDmMedia($relation->getAlias());
			}
			else
			{
				if ($relation instanceof Doctrine_Relation_ForeignKey)
				{
					if ($this->getRelationHolder()->getAssociationByRefClass($relation->getClass()))
					{
						continue;
					}
				}

				$joinAlias = dmString::lcfirst($relation->getAlias());
				$query->leftJoin(sprintf('%s.%s %s', $rootAlias, $relation->getAlias(), $joinAlias));

				if($relation->getTable()->hasRelation('Translation'))
				{
					$joinI18nAlias = $joinAlias.'Translation';
					$query->leftJoin(sprintf('%s.%s %s WITH %s.lang = ?', $joinAlias, 'Translation', $joinI18nAlias, $joinI18nAlias), dmDoctrineRecord::getDefaultCulture());
				}
			}
		}

		return $query;
	}

	/**
	 * Will join named relations
	 * @param dmDoctrineQuery $query
	 * @param array $aliases
	 * @param boolean $with18n
	 */
	public function joinRelations($query, array $aliases, $withI18n = false)
	{
		$rootAlias = $query->getRootAlias();

		foreach($aliases as $alias)
		{
			if (!$relation = $this->getRelationHolder()->get($alias))
			{
				throw new dmException(sprintf('%s is not a valid alias for the table %s', $alias, $this->getComponentName()));
			}

			if ($relation->getAlias() === 'Translation')
			{
				$query->withI18n();
			}
			elseif ($relation->getClass() === 'DmMedia')
			{
				$mediaJoinAlias = dmString::lcfirst($relation->getAlias());
				$query->leftJoin(sprintf('%s.%s %s', $rootAlias, $relation->getAlias(), $mediaJoinAlias))
				->leftJoin(sprintf('%s.%s %s', $mediaJoinAlias, 'Folder', $mediaJoinAlias.'Folder'));
			}
			else
			{
				$joinAlias = dmString::lcfirst($relation->getAlias());
				$query->leftJoin(sprintf('%s.%s %s', $rootAlias, $relation->getAlias(), $joinAlias));

				if($withI18n && $relation->getTable()->hasI18n())
				{
					$joinTranslationAlias = $joinAlias.'Translation';
					$query->leftJoin($joinAlias.'.Translation '.$joinTranslationAlias.' ON '.$joinAlias.'.id = '.$joinTranslationAlias.'.id AND '.$joinTranslationAlias.'.lang = ?', myDoctrineRecord::getDefaultCulture());
				}
			}
		}

		return $this;
	}

	/**
	 * @return dmDoctrineQuery the default admin list query
	 */
	public function getAdminListQuery(dmDoctrineQuery $query)
	{
		$relations = $this->getOption('admin.query.relations');
		if(null === $relations){ $relations = array(); }
		$this
		->joinLocals($query->withI18n(null, $this->getComponentName()), true)
		->joinRelations($query, $relations, true);

		if ($this->isNestedSet()) {
			if ($this->getTemplate('NestedSet')->getOption('hasManyRoots')) {
				$query->addOrderBy($this->getTemplate('NestedSet')->getOption('rootColumnName', 'root_id') . ' ASC');
			}
			$query->addOrderBy('lft ASC');
		} elseif ($this->isSortable()) {
			$query->addOrderBy('position ASC');
		}
		return $query;
	}

	/**
	 * add i18n columns if needed
	 */
	public function getAllColumns()
	{
		$columns = $this->getColumns();

		if($this->hasI18n())
		{
			$columns = array_merge($columns, $this->getI18nTable()->getColumns());
		}

		return $columns;
	}

	public function hasField($fieldName)
	{
		if (isset($this->_columnNames[$fieldName]))
		{
			return true;
		}

		if ($this->hasI18n() && $this->getI18nTable()->hasField($fieldName))
		{
			return true;
		}

		return false;
	}

	/**
	 * Return columns that a human can fill
	 * Will exclude primary key, timestampable fields
	 */
	public function getHumanColumns()
	{
		if ($this->hasCache('human_columns'))
		{
			return $this->getCache('human_columns');
		}

		$columns = $this->getAllColumns();
		foreach((array)$this->getOption('inheritanceMap') as $field => $value) {
			 unset($columns[$field]);
		}

		if ($this->isVersionable())
		{
			unset($columns['version']);
		}
		if($this->isSortable())
		{
			unset($columns['position']);
		}

		foreach($columns as $columnName => $column)
		{
			if (!empty($column['autoincrement']) || in_array($columnName, array('created_at', 'updated_at', 'id')))
			{
				unset($columns[$columnName]);
			}
		}

		return $this->setCache('human_columns', $columns);
	}

	public function getSeoColumns()
	{
		$columns = array_keys($this->getHumanColumns());

		$columns = array();

		foreach($this->getHumanColumns() as $columnName => $column)
		{
			if (in_array($column['type'], array('string', 'blob', 'clob', 'enum')))
			{
				$columns[] = $columnName;
			}
		}

		if ($pk = $this->getPrimaryKey())
		{
			$columns[] = $pk;
		}

		$columns = $this->getEventDispatcher()->filter(
		new sfEvent($this, 'dm.table.filter_seo_columns'),
		$columns
		)->getReturnValue();

		return array_unique(array_filter($columns));
	}

	public function getIndexableColumns()
	{
		$columns = $this->getHumanColumns();

		foreach($columns as $columnName => $column)
		{
			if(in_array($column['type'], array('time', 'timestamp', 'boolean')))
			{
				unset($columns[$columnName]);
			}
		}

		return $columns;
	}

	public function getAllColumnNames()
	{
		return array_keys($this->getAllColumns());
	}

	public function getHumanColumnNames()
	{
		return array_key($this->getHumanColumns());
	}

	public function getColumn($columnName)
	{
		return dmArray::get($this->getAllColumns(), $columnName);
	}

	public function isSortable()
	{
		return ($this->hasTemplate('DmSortable') || $this->hasTemplate('Sortable')) && 'id' === $this->getPrimaryKey();
	}

	public function isVersionable()
	{
		return $this->hasTemplate('DmVersionable') || ($this->hasI18n() && $this->getI18nTable()->hasTemplate('DmVersionable'));
	}

	public function isNestedSet()
	{
		return $this->hasTemplate('NestedSet');
	}

	public function hasI18n()
	{
		return $this->hasI18n;
	}

	public function getI18nTable()
	{
		if ($this->hasCache('i18n_table'))
		{
			return $this->getCache('i18n_table');
		}

		return $this->setCache('i18n_table', $this->hasI18n()
		? $this->getRelationHolder()->get('Translation')->getTable()
		: false
		);
	}

	/**
	 * Retrieves a column definition from this table schema.
	 *
	 * @param string $columnName
	 * @return array              column definition; @see $_columns
	 */
	public function getColumnDefinition($columnName)
	{
		$columnDefinition = parent::getColumnDefinition($columnName);

		if (!$columnDefinition && $this->hasI18n())
		{
			$columnDefinition = $this->getI18nTable()->getColumnDefinition($columnName);
		}

		return $columnDefinition;
	}

	public function isMarkdownColumn($columnName)
	{
		return strpos(dmArray::get($this->getColumnDefinition($columnName), 'extra', ''), 'markdown') !== false;
	}

	public function isPaginatedColumn($columnName)
	{
		return strpos(dmArray::get($this->getColumnDefinition($columnName), 'extra', ''), 'paginated') !== false;
	}

	public function isBooleanColumn($columnName)
	{
		return 'boolean' === dmArray::get($this->getColumnDefinition($columnName), 'type');
	}

	public function isI18nColumn($columnName)
	{
		return !isset($this->_columnNames[$columnName]) && $this->hasI18n() && $this->getI18nTable()->hasField($columnName);
	}

	public function isLinkColumn($columnName)
	{
		return false !== strpos(dmArray::get($this->getColumnDefinition($columnName), 'extra', ''), 'link');
	}

	/**
	 * Tries to find a column name that could be used to represent a record of this table
	 */
	public function getIdentifierColumnName()
	{
		if ($this->hasCache('dm_identifier_column_name'))
		{
			return $this->getCache('dm_identifier_column_name');
		}

		if (!$columnName = dmArray::first(array_intersect(sfConfig::get('dm_orm_identifier_fields'), $this->getAllColumnNames())))
		{
			if (!$columnName = dmArray::first($this->getIdentifierColumnNames()))
			{
				$columnName = dmArray::first($this->getColumnNames());
			}
		}

		return $this->setCache('dm_identifier_column_name', $columnName);
	}

	public function getPrimaryKeys()
	{
		if ($this->hasCache('dm_primary_keys'))
		{
			return $this->getCache('dm_primary_keys');
		}

		$primaryKeys = array();

		foreach($this->getColumns() as $columnName => $column)
		{
			if (!empty($column['primary']))
			{
				$primaryKeys[] = $columnName;
			}
		}

		return $this->setCache('dm_primary_keys', $primaryKeys);
	}

	/**
	 * Will return pk column name if table has only one pk, or null
	 */
	public function getPrimaryKey()
	{
		if (count($this->getPrimaryKeys()) === 1)
		{
			return dmArray::first($this->getPrimaryKeys());
		}

		return null;
	}

	/**
	 * @return dmTableRelationHolder the table relation holder
	 */
	public function getRelationHolder()
	{
		if ($this->hasCache('dm_relation_holder'))
		{
			return $this->getCache('dm_relation_holder');
		}

		return $this->setCache('dm_relation_holder', new dmTableRelationHolder($this));
	}

	/**
	 * Reorders a set of sortable objects based on a list of id/position
	 * Beware that there is no check made on the positions passed
	 * So incoherent positions will result in an incoherent list
	 *
	 * @param array id/position pairs
	 *
	 * @return Boolean true if the reordering took place, false if a database problem prevented it
	 **/
	public function doSort(array $order)
	{
		if (!$this->hasField('position'))
		{
			throw new dmException(sprintf('%s table has no position field', $this->getComponentName()));
		}

		$records = $this->createQuery('q INDEXBY q.id')->whereIn('q.id', array_keys($order))->fetchRecords();
		$modifiedRecords = new Doctrine_Collection($this);

		foreach ($order as $id => $position)
		{
			if ($position != $records[$id]->get('position'))
			{
				$records[$id]->set('position', $position);
				$modifiedRecords[] = $records[$id];
			}
		}

		$modifiedRecords->save();

		unset($records, $modifiedRecords);
	}

	/**
	 * return dmModule this record module
	 */
	public function getDmModule()
	{
		if($this->hasCache('dm_module'))
		{
			return $this->getCache('dm_module');
		}

    if (!$this->getModuleManager() instanceof DmModuleManager)
    {
      return null;
    }

		return $this->setCache('dm_module', $this->getModuleManager()->getModuleByModel($this->getComponentName()));
	}
	/**
	 * Usefull for generators ( admin, form, filter )
	 */
	public function getSfDoctrineColumns()
	{
		$columns = array();

		foreach ($this->getAllColumnNames() as $name)
		{
			$columns[$name] = new sfDoctrineColumn($name, $this);
		}

		return $columns;
	}

	public function getSfDoctrineColumn($name)
	{
		return $this->hasColumn($name) ? new sfDoctrineColumn($name, $this) : false;
	}

	/**
	 * dmMicroCache
	 */
	protected
	$cache;

	protected function getCache($cacheKey)
	{
		if(isset($this->cache[$cacheKey]))
		{
			return $this->cache[$cacheKey];
		}

		return null;
	}

	protected function hasCache($cacheKey)
	{
		return isset($this->cache[$cacheKey]);
	}

	protected function setCache($cacheKey, $cacheValue)
	{
		return $this->cache[$cacheKey] = $cacheValue;
	}

	public function clearCache($cacheKey = null)
	{
		if (null === $cacheKey)
		{
			$this->cache = array();
		}
		elseif(isset($this->cache[$cacheKey]))
		{
			unset($this->cache[$cacheKey]);
		}

		return $this;
	}


	public static function setEventDispatcher(sfEventDispatcher $eventDispatcher)
	{
		self::$eventDispatcher = $eventDispatcher;
	}

	/**
	 * @param dmBaseServiceContainer $serviceContainer
	 */
	public static function setServiceContainer(dmBaseServiceContainer $serviceContainer)
	{
		self::$serviceContainer = $serviceContainer;
		self::setModuleManager($serviceContainer->getService('module_manager'));
		self::setEventDispatcher($serviceContainer->getService('dispatcher'));
	}

	/**
	 * @param dmModuleManager $moduleManager
	 */
	public static function setModuleManager(dmModuleManager $moduleManager)
	{
		self::$moduleManager = $moduleManager;
	}

	/**
	 *
	 * @return sfEventDispatcher
	 */
	public function getEventDispatcher()
	{
		return self::$eventDispatcher;
	}

	/**
	 *
	 * @return sfServiceContainer
	 */
	public function getServiceContainer()
	{
		return self::$serviceContainer;
	}

	public function getService($name, $class = null)
	{
		if($sc = $this->getServiceContainer())
		{
			return $sc->getService($name, $class);
		}

		return null;
	}

	/**
	 * @return dmModuleManager
	 */
	public function getModuleManager()
	{
		return self::$moduleManager;
	}

	/**
	 * (non-PHPdoc)
	 * @see Doctrine_Table::createQuery()
	 * @return dmDoctrineQuery
	 */
	public function createQuery($alias = '')
	{
		return parent::createQuery($alias);
	}

	/**
	 * @param Doctrine_Relation_Association $relation
	 * @return Doctrine_Relation_Association the opposite relation
	 */
	public function getAssociationOppositeRelation($relation)
	{
		$relation = is_string($relation) ? $relation['localTable']->getRelation($relationName) : $relation;
		$relatedTable = $relation->getTable();
		$local = $relation['local'];
		$foreign = $relation['foreign'];

		foreach($relatedTable->getRelations() as $relatedTableRelation)
		{
			if($relatedTableRelation['foreign'] === $local)
			{
				return $relatedTableRelation;
			}
		}
		return false;
	}

	/**
	 * Returns the name of the model class this model extends.
	 *
	 * @return string|null
	 */
	public function getParentModel()
	{
		$baseClasses = array(
      'Doctrine_Record',
      'sfDoctrineRecord',
		);

		$builderOptions = sfConfig::get('doctrine_model_builder_options', array());
		if (isset($builderOptions['baseClassName']))
		{
			$baseClasses[] = $builderOptions['baseClassName'];
		}

		// find the first non-abstract parent
		$model = $this->_options['name'];
		while ($model = get_parent_class($model))
		{
			if (in_array($model, $baseClasses))
			{
				break;
			}

			$r = new ReflectionClass($model);
			if (!$r->isAbstract())
			{
				return $r->getName();
			}
		}
	}
	/**
	 * 
	 * Get the opposite relation of given relation
	 * @param string $relation 
	 * @throws LogicException
	 * @return Doctrine_Relation 
	 */
	public function getOppositeRelation($relation)
	{
		if(is_string($relation))
		{
			$relation = $this->getRelation($relation);
		}
		if(!$relation instanceof Doctrine_Relation)
		{
			throw new LogicException('Given $relation is not a Doctrine_Relation');
		}
		
		$oppositeTableRelations = $relation['table']->getRelationParser()->getRelations();
		
		foreach($oppositeTableRelations as $rel)
		{
			if($rel['local'] === $relation['foreign'])
			{
				return $rel;
			}
		}
		return false;
	}
}