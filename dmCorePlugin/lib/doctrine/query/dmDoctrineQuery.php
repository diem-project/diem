<?php

abstract class dmDoctrineQuery extends Doctrine_Query
{
	/**
	 * @var dmModuleManager
	 */
	protected static $moduleManager;


	/**
	 * Constructor.
	 *
	 * @param Doctrine_Connection  The connection object the query will use.
	 * @param Doctrine_Hydrator_Abstract  The hydrator that will be used for generating result sets.
	 */
	public function __construct(Doctrine_Connection $connection = null, Doctrine_Hydrator_Abstract $hydrator = null)
	{
		parent::__construct($connection, $hydrator);
		if (function_exists('dql_tokenize_query'))
		{
			//@todo is this a good name for this ?
			$this->_tokenizer = new myDoctrineQueryTokenizer();
		}
	}


	/**
	 * Join translation results if they exist
	 * if $model is specified, will verify that it has I18n
	 * return @myDoctrineQuery $this
	 */
	public function withI18n($culture = null, $model = null, $rootAlias = null, $joinSide = 'left')
	{
		if (null === $model)
		{
			$_rootAlias = $this->getRootAlias();
			$from = explode(' ', $this->_dqlParts['from'][0]);
			$model = $from[0];
			if(strlen($model) === 0){
				$this->getRootAlias();
				$models = array_keys($this->_queryComponents);
				$model = $models[0];
			}
		}

		if (!dmDb::table($model)->hasI18n())
		{
			return $this;
		}

		$culture = null === $culture ? myDoctrineRecord::getDefaultCulture() : $culture;

		if (null === $rootAlias)
		{
			// refresh query for introspection
			if(empty($this->_execParams))
			{
				//prevent bugs for subqueries
				$this->fixArrayParameterValues($this->_params);
			}
			$this->buildSqlQuery();

			$rootAlias        = $this->getRootAlias();
			$translationAlias = $rootAlias.'Translation';

			// i18n already joined
			if ($this->hasAliasDeclaration($translationAlias))
			{
				return $this;
			}
		}
		else
		{
			$translationAlias = $rootAlias.'Translation';
		}

		$joinMethod = $joinSide.'Join';

		return $this->$joinMethod($rootAlias.'.Translation '.$translationAlias.' ON '.$rootAlias.'.id = '.$translationAlias.'.id AND '.$translationAlias.'.lang = ?', $culture);
	}

	/**
	 * Join media for this columnName or alias
	 * return @dmDoctrineQuery $this
	 */
	public function withDmMedia($alias, $rootAlias = null)
	{
		$rootAlias = $rootAlias ? $rootAlias : $this->getRootAlias();
		$mediaJoinAlias = $rootAlias.dmString::camelize($alias);
		$folderJoinAlias = $mediaJoinAlias.'Folder';

		return $this->leftJoin(sprintf('%s.%s %s, %s.%s %s', $rootAlias, $alias, $mediaJoinAlias, $mediaJoinAlias, 'Folder', $folderJoinAlias));
	}

	public function whereIsActive($boolean = true, $model = null)
	{
		if (null !== $model)
		{
			$table = dmDb::table($model);

			if (!$table->hasField('is_active'))
			{
				return $this;
			}

			if($table->isI18nColumn('is_active'))
			{
				// will join i18n if missing
				$this->withI18n();

				$translationAlias = $this->getRootAlias().'Translation';

				return $this->addWhere($translationAlias.'.is_active = ?', (bool) $boolean);
			}
		}

		return $this->addWhere($this->getRootAlias().'.is_active = ?', (bool) $boolean);
	}

	/**
	 * Will restrict results to $model records
	 * associated with $ancestor record
	 */
	public function whereAncestor(myDoctrineRecord $ancestorRecord, $model)
	{
		return $this->whereAncestorId(get_class($ancestorRecord), $ancestorRecord->get('id'), $model);
	}

	/**
	 * Will restrict results to $model records
	 * associated with $ancestorModel->$ancestorId record
	 */
	#TODO optimize speed by not fetching $ancestorRecord
	public function whereAncestorId($ancestorRecordModel, $ancestorRecordId, $model)
	{
		if(!$module = self::$moduleManager->getModuleByModel($model))
		{
			throw new dmException(sprintf('No module with model %s', $model));
		}

		$ancestorModule = self::$moduleManager->getModuleByModel($ancestorRecordModel);

		if ($module->hasLocal($ancestorModule))
		{
			$this->addWhere(sprintf('%s.%s = ?',
			$this->getRootAlias(),
			$module->getTable()->getRelationHolder()->getLocalByClass($ancestorRecordModel)->getLocal()
			),
			$ancestorRecordId
			);
		}
		elseif ($module->hasAssociation($ancestorModule))
		{
			$this->leftJoin(sprintf('%s.%s %s',
			$this->getRootAlias(),
			$module->getTable()->getRelationHolder()->getByClass($ancestorRecordModel)->getAlias(),
			$ancestorModule->getKey()
			))
			->addWhere($ancestorModule->getKey().'.id = ?', $ancestorRecordId);
		}
		elseif($module->hasAncestor($ancestorModule))
		{
			$current      = $module;
			$currentAlias = $this->getRootAlias();

			foreach(array_reverse($module->getPath(), true) as $ancestorKey => $ancestor)
			{
				if (!$relation = $current->getTable()->getRelationHolder()->getByClass($ancestor->getModel()))
				{
					throw new dmRecordException(sprintf('%s has no relation for class %s', $current, $ancestor->getModel()));
					return null;
				}

				$this->leftJoin($currentAlias.'.'.$relation->getAlias().' '.$ancestorKey);

				if ($ancestor->is($ancestorModule))
				{
					break;
				}

				$current       = $ancestor;
				$currentAlias  = $ancestor->getKey();
			}

			$this->addWhere($ancestorModule->getKey().'.id = ?', $ancestorRecordId);
		}
		else
		{
			throw new dmRecordException(sprintf('%s is not an ancestor of %s, nor associated', $ancestorRecordModel, $module));
			return null;
		}

		return $this;
	}

	/**
	 * Will restrict results to $model records
	 * associated with $descendant record
	 */
	public function whereDescendant(myDoctrineRecord $descendantRecord, $model)
	{
		return $this->whereDescendantId(get_class($descendantRecord), $descendantRecord->get('id'), $model);
	}

	/**
	 * Will restrict results to $model records
	 * associated with $descendantModel->$descendantId record
	 */
	public function whereDescendantId($descendantRecordModel, $descendantRecordId, $model)
	{
		if(!$module = self::$moduleManager->getModuleByModel($model))
		{
			throw new dmException(sprintf('No module %s', $model));
		}

		if($descendantRecordModel == $model)
		{
			return $this->addWhere($this->getRootAlias().'.id = ?', $descendantRecordId);
		}

		if(!$descendantModule = $module->getDescendant($descendantRecordModel))
		{
			throw new dmRecordException(sprintf('%s is not an descendant of %s', $descendantRecordModel, $module));
		}

		$parent       = $module;
		$parentAlias  = $this->getRootAlias();

		foreach($descendantModule->getPathFrom($module, true) as $descendantKey => $descendant)
		{
			if ($descendantKey != $module->getKey())
			{
				if (!$relation = $parent->getTable()->getRelationHolder()->getByClass($descendant->getModel()))
				{
					throw new dmRecordException(sprintf('%s has no relation for class %s', $parent, $descendant->getModel()));
				}

				$this->leftJoin($parentAlias.'.'.$relation['alias'].' '.$descendantKey);

				if ($descendant->is($module))
				{
					break;
				}

				$parent        = $descendant;
				$parentAlias   = $descendantKey;
			}
		}

		$this->addWhere($descendantModule->getKey().'.id = ?', $descendantRecordId);

		return $this;
	}

	/**
	 * Add asc order by position field
	 * if $model is specified, will verify that it has I18n
	 * @return myDoctrineQuery $this
	 */
	public function orderByPosition($model = null)
	{
		if (null !== $model)
		{
			if (!dmDb::table($model)->hasField('position'))
			{
				return $this;
			}
		}

		$me = $this->getRootAlias();

		return $this
		->addOrderBy("$me.position asc");
	}

	/**
	 * returns join alias for a given relation alias, if joined
	 * ex: "Elem e, e.Categ my_categ"
	 * alias for joined relation Categ = my_categ
	 * getJoinAliasForRelationAlias('Elem', 'Categ') ->my_categ
	 */
	public function getJoinAliasForRelationAlias($model, $relationAlias)
	{
		$this->buildSqlQuery();

		foreach ($this->getQueryComponents() as $joinAlias => $queryComponent)
		{
			if (
			isset($queryComponent['relation'])
			&& $relationAlias == $queryComponent['relation']['alias']
			&& $model == $queryComponent['relation']['localTable']->getComponentName()
			)
			{
				return $joinAlias;
			}
		}

		return null;
	}

	/**
	 * @return myDoctrineCollection|null the fetched collection
	 */
	public function fetchRecords($params = array())
	{
		return $this->execute($params, Doctrine_Core::HYDRATE_RECORD);
	}

	/**
	 * Add limit(1) to the query,
	 * then execute $this->fetchOne()
	 * @return myDoctrineRecord|null the fetched record
	 */
	public function fetchRecord($params = array(), $hydrationMode = Doctrine_Core::HYDRATE_RECORD)
	{
		return $this->limit(1)->fetchOne($params, $hydrationMode);
	}

	public function fetchValue($params = array())
	{
		return $this->execute($params, Doctrine_Core::HYDRATE_SINGLE_SCALAR);
	}

	public function fetchValues($params = array())
	{
		return $this->execute($params, Doctrine_Core::HYDRATE_SCALAR);
	}

	public function fetchOneArray($params = array())
	{
		return $this->fetchOne($params, Doctrine_Core::HYDRATE_ARRAY);
	}

	/**
	 * fetch brutal PDO array with numeric keys
	 * @return array PDO result
	 */
	public function fetchPDO($params = array())
	{
		return $this->execute($params, Doctrine_Core::HYDRATE_NONE);
	}

	/**
	 * fetch brutal flat array with numeric keys
	 * @return array PDO result
	 */
	public function fetchFlat($params = array())
	{
		return $this->execute($params, 'dmFlat');
	}

	public function exists()
	{
		return $this->count() > 0;
	}

	public function toDebug()
	{
		return $this->getSqlQuery();
	}

	public static function setModuleManager(dmModuleManager $moduleManager)
	{
		self::$moduleManager = $moduleManager;
	}
	
		/**
	 * 
	 * @return dmDoctrineQuery
	 */
	public function andWhere($where, $params = array())
	{
		return parent::andWhere($where, $params);
	}
	
}