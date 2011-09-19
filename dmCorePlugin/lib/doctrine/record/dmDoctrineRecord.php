<?php

abstract class dmDoctrineRecord extends sfDoctrineRecord
{
	protected static
	$eventDispatcher,
	$serviceContainer,
	$moduleManager;

	protected
	$i18nFallback = null;

	/**
	 * Doctrine nestedSet helper
	 * @return integer
	 */
	public function getNestedSetParent() {
		if ($this->getTable()->isNestedSet())
		{
			if (!$this->getNode()->isValidNode() || $this->getNode()->isRoot()) {
				return null;
			}

			return $this->getNode()->getParent();

		}
		return Doctrine_Null;
	}

	public function getNestedSetParentId() {
		if ($parent = $this->getNestedSetParent()) {
			return $parent->id;
		}
	}

	public function getNestedSetIndentedName()
	{
		if ($this->getTable()->isNestedSet()) {
			return str_repeat('--', $this->level) . ' ' . $this;
		}
	}

	/**
	 * Initializes internationalization.
	 *
	 * @see Doctrine_Record
	 */
	public function construct()
	{
		if ($this->getTable()->hasI18n())
		{
			// only add filter to each table once
			if (!$this->getTable()->getOption('has_symfony_i18n_filter'))
			{
				$this->getTable()
				->unshiftFilter(new dmDoctrineRecordI18nFilter())
				->setOption('has_symfony_i18n_filter', true)
				;
			}
		}
	}

	public function hasCurrentTranslation()
	{
		return $this->get('Translation')->contains(self::getDefaultCulture());
	}

	public function getCurrentTranslation()
	{
		return $this->get('Translation')->get(self::getDefaultCulture());
	}

	/**
	 * Add page tree watcher registering
	 */
	public function preSave($event)
	{
		parent::preSave($event);

		if ($this->isModified())
		{
			$this->notify($this->isNew() ? 'create' : 'update');
		}
		elseif($this->getTable()->hasI18n() && $this->hasCurrentTranslation() && $this->getCurrentTranslation()->isModified())
		{
			$this->notify($this->isNew() ? 'create' : 'update');
		}
	}

	/**
	 * Notify insertion
	 */
	public function postInsert($event)
	{
		if(!$this instanceof dmPage && $this->getDmModule() && $this->getDmModule()->getOption('has_security', false))
		{
			$this->getService('record_security_manager')->manage('insert', $this);
		}
		if ($ed = $this->getEventDispatcher())
		{
			$ed->notify(new sfEvent($this, 'dm.record.creation'));
		}
	}

	/**
	 * Add page tree watcher registering
	 */
	public function postDelete($event)
	{
		parent::postDelete($event);
		if(!$this instanceof dmPage && $this->getDmModule() && $this->getDmModule()->getOption('has_security', false))
		{
			$this->getService('record_security_manager')->manage('delete', $this);
		}
		$this->notify('delete');
	}


	public function notify($type = 'update')
	{
		if ($ed = $this->getEventDispatcher())
		{
			$ed->notify(new sfEvent($this, 'dm.record.modification', array('type' => $type)));
		}
		if(!$this instanceof dmPage && $this->getDmModule() && $this->getDmModule()->getOption('has_security', false))
		{
			$this->getService('record_security_manager')->manage($type, $this);
		}
	}

	public function refresh($deep = false)
	{
		return parent::refresh($deep)->clearCache();
	}


	public function getPrevNextRecords(dmDoctrineQuery $q)
	{
		/*
		 * Will not work if table has composite primary key
		 */
		if (!$pk = $this->_table->getPrimaryKey())
		{
			return null;
		}

		$qPk = clone $q;

		$qPk->select($qPk->getRootAlias().'.'.$pk)/*->distinct()*/;

		$pks = array_values(array_unique($qPk->fetchFlat()));

		$recordOffset = array_search($this->getPrimaryKey(), $pks);

		$map = array(
      'prev' => 0 == $recordOffset ? null : (isset($pks[$recordOffset-1]) ? $pks[$recordOffset-1] : null),
      'next' => count($pks) == ($recordOffset+1) ? null : (isset($pks[$recordOffset+1]) ? $pks[$recordOffset+1] : null)
		);

		$pks = array_unique(array_filter(array_values($map)));

		$records = empty($pks) ? array() : $this->_table->createQuery('q INDEXBY q.'.$pk)->whereIn('q.'.$pk, $pks)->fetchRecords();

		foreach($map as $key => $id)
		{
			$map[$key] = isset($records[$id]) ? $records[$id] : null;
		}

		return $map;
	}

	/**
	 * Tries to return the nearest records in table
	 */
	public function getNearRecords(dmDoctrineQuery $q, $nb = 11)
	{
		/*
		 * Ensure nb is not pair
		 */
		if (!$nb%2) $nb++;

		/*
		 * Will not work if table has composite primary key
		 */
		if (!$pk = $this->_table->getPrimaryKey())
		{
			return null;
		}

		$qPk = clone $q;
		$qPk->removeDqlQueryPart('join')->removeDqlQueryPart('from')->from(get_class($this).' '.$qPk->getRootAlias());
		$qPk->select($qPk->getRootAlias().'.'.$pk)->distinct();
		$pks = $qPk->fetchPDO();
		foreach($pks as $key => $attrs)
		{
			$pks[$key] = array_shift($attrs);
		}

		if (count($pks) > $nb)
		{
			$offsetRange = ($nb-1)/2;
			$recordOffset = array_search($this->getPrimaryKey(), $pks);
			$minOffset = max(0, $recordOffset-$offsetRange);
			$maxOffset = min(count($pks)-1, $recordOffset+$offsetRange+1);

			$selectedPks = array_slice($pks, $minOffset, $maxOffset-$minOffset);
		}
		else
		{
			$selectedPks = $pks;
		}

		$rPk = clone $q;
		$qPk->removeDqlQueryPart('join');
		$rPk->whereIn($rPk->getRootAlias().'.'.$pk, $selectedPks);

		//    dmDebug::kill($rPk->getSqlQuery(), $rPk->fetchRecords());

		return $rPk->fetchRecords();
	}

	/**
	 * Tries to return the previous record in table
	 */
	public function getPreviousRecord(Doctrine_Collection $nearRecords)
	{
		foreach($nearRecords as $key => $record)
		{
			if ($this->getOid() === $record->getOid())
			{
				$myOffset = $key;
				break;
			}
		}

		if (!isset($myOffset) || $myOffset === 0)
		{
			return null;
		}

		return $nearRecords[$myOffset-1];
	}

	/**
	 * Tries to return the next record in table
	 */
	public function getNextRecord(Doctrine_Collection $nearRecords)
	{
		foreach($nearRecords as $key => $record)
		{
			if ($this->getOid() === $record->getOid())
			{
				$myOffset = $key;
				break;
			}
		}

		if (!isset($myOffset) || $myOffset === ($nearRecords->count()-1))
		{
			return null;
		}

		return $nearRecords[$myOffset+1];
	}

	/**
	 * add fluent interface to @see parent::fromArray
	 * @return myDoctrineRecord $this ( fluent interface )
	 */
	public function fromArray(array $array, $deep = true)
	{
		parent::fromArray($array, $deep);
		return $this;
	}

	/**
	 * @return DmMediaFolder the DmMediaFolder used to store this record's medias
	 */
	public function getDmMediaFolder()
	{
		return $this->_table->getDmMediaFolder();
	}

	/**
	 * @return DmMedia the associated media for this columnName or null
	 */
	public function getDmMediaByColumnName($columnName)
	{
		$relation = $this->_table->getRelationHolder()->getLocalByColumnName($columnName);

		if (!$relation instanceof Doctrine_Relation_LocalKey)
		{
			throw new dmException(sprintf('%s is not a DmMedia LocalKey columnName : %s', $columnName, get_class($relation)));
		}

		if ($relation->getClass() != 'DmMedia')
		{
			throw new dmException(sprintf('%s is not a DmMedia relation', $relation->getAlias()));
		}

		if(!$media = $this->get($relation['alias']))
		{
			if($media = dmDb::table('DmMedia')->findOneByIdWithFolder($this->get($columnName)))
			{
				$this->set($relation['alias'], $media);
			}
		}

		return $media;
		//    return $relation->fetchRelatedFor($this);
	}

	/**
	 * sets a DmMedia record associated to this columnName
	 * @return DmMedia the newly associated media for this columnName
	 */
	public function setDmMediaByColumnName($columnName, DmMedia $media)
	{
		$relation = $this->_table->getRelationHolder()->getLocalByColumnName($columnName);

		if (!$relation instanceof Doctrine_Relation_LocalKey)
		{
			throw new dmException(sprintf('%s is not a DmMedia LocalKey columnName', $columnName));
		}

		if (!$relation['class'] === 'DmMedia')
		{
			throw new dmException(sprintf('%s is not a DmMedia LocalKey columnName', $columnName));
		}

		return $this
		->set($relation['alias'], $media)
		->set($columnName, $media ? $media->get('id') : null);
	}

	/**
	 * @return dmModule this record module
	 */
	public function getDmModule()
	{
		return $this->_table->getDmModule();
	}

	/**
	 * @return dmPage this record page
	 */
	public function getDmPage()
	{
		if($this->getTable()->hasRelation('DmPage'))
		{
			return $this->_get('DmPage');
		}

		if(!$this->getDmModule() || !$this->getDmModule()->hasPage())
		{
			throw new dmRecordException(sprintf('record %s has no page because module %s has no page', get_class($this), $this->getDmModule()));
		}

		if($page = dmDb::table('DmPage')->findOneByRecordWithI18n($this))
		{
			return $page;
		}

		// The record has no page yet, let's try to create it right now
		$event = new sfEvent($this, 'dm.record.page_missing', array());

		$this->getEventDispatcher()->notifyUntil($event);

		if($event->isProcessed())
		{
			$page = $event->getReturnValue();
		}

		return $page;
	}

	/**
	 * @return boolean true if this record has a page, false otherwise
	 */
	public function hasDmPage()
	{
		return $this->getDmModule() && $this->getDmModule()->hasPage() && dmDb::table('DmPage')->findOneByRecordWithI18n($this);
	}

	/**
	 * Will find in module ancestors the requested model
	 * and return the ancestor record for this model
	 * @return myDoctrineRecord the ancestor record
	 */
	public function getAncestorRecord($model, $hydrationMode = Doctrine_Core::HYDRATE_RECORD)
	{
		if (get_class($this) == $model)
		{
			return $this;
		}

		$ancestorModule = $this->getModuleManager()->getModuleByModel($model);

		if(!$ancestorModule || !$this->getDmModule()->hasAncestor($ancestorModule))
		{
			throw new dmRecordException(sprintf('%s is not an ancestor of %s', $model, $this->getDmModule()));
		}

		return $ancestorModule->getTable()->createQuery($ancestorModule->getKey())
		->whereDescendantId($this->getDmModule()->getModel(), $this->get('id'), $ancestorModule->getModel())
		->withI18n(null, $model, $ancestorModule->getKey())
		->fetchOne();
	}

	/**
	 * Will find in module ancestors the requested model
	 * and return the ancestor record id for this model
	 * @return int the ancestor record id
	 */
	public function getAncestorRecordId($model)
	{
		if (get_class($this) == $model)
		{
			return $this->get('id');
		}

		$ancestorModule = $this->getModuleManager()->getModuleByModel($model);

		if(!$ancestorModule || !$this->getDmModule()->hasAncestor($ancestorModule))
		{
			throw new dmRecordException(sprintf('%s is not an ancestor of %s', $model, $this->getDmModule()));
		}

		$id = $ancestorModule->getTable()->createQuery($ancestorModule->getKey())
		->whereDescendantId($this->getDmModule()->getModel(), $this->get('id'), $ancestorModule->getModel())
		->select($ancestorModule->getKey().'.id')
		->fetchValue();

		if(is_array($id))
		{
			$id = array_shift($id);
		}

		return $id;
	}

	/**
	 * Returns one record related to this one by $alias
	 * LocalKey relation : the related record
	 * ForeignKey && Association relation : the first related record
	 * @return myDoctrineRecord|null the related record or null if not exist
	 */
	public function getRelatedRecord($class, $hydrationMode = Doctrine_Core::HYDRATE_RECORD)
	{
		if (!$relation = $this->_table->getRelationHolder()->getByClass($class))
		{
			throw new dmRecordException(sprintf('%s has no relation for class %s', get_class($this), $class));
		}

		if ($relation instanceof Doctrine_Relation_LocalKey)
		{
			return $relation['table']->createQuery('dm_foreign')
			->where('dm_foreign.id  = ?', $this->get($relation->getLocal()))
			->withI18n(null, $class, 'dm_foreign')
			->fetchOne(array(), $hydrationMode);
		}
		elseif($relation instanceof Doctrine_Relation_ForeignKey)
		{
			return $relation['table']->createQuery('dm_foreign')
			->where('dm_foreign.'.$relation->getForeignColumnName().' = ?', $this->get('id'))
			->withI18n(null, $class, 'dm_foreign')
			->fetchOne(array(), $hydrationMode);
		}
		elseif($relation instanceof Doctrine_Relation_Association)
		{
			return $relation['table']->createQuery('dm_foreign')
			->leftJoin('dm_foreign.'.$relation['refTable']->getComponentName().' ref_table')
			->where('ref_table.'.$relation->getLocal().' = ?', $this->get('id'))
			->withI18n(null, $class, 'dm_foreign')
			->fetchOne(array(), $hydrationMode);
		}
		else
		{
			throw new dmException('Strange relation...');
		}
	}

	/**
	 * Return null if this record is new
	 * @return dmDoctrineRecord | null
	 */
	public function orNull()
	{
		return $this->isNew() ? null : $this;
	}

	/**
	 * Returns a string representation of the record.
	 *
	 * @return string A string representation of the record.
	 */
	public function __toString()
	{
		try
		{
			$string = $this->get($this->table->getIdentifierColumnName());
		}
		catch(Exception $e)
		{
			$this->getServiceContainer()->getService('logger')->err($e->getMessage());

			if (sfConfig::get('dm_debug'))
			{
				throw $e;
			}

			$string = '';
		}

		if (empty($string))
		{
			$string = '-';
		}

		return $string;
	}

	/**
	 * Shortcut to __toString method
	 * used by admin generator
	 */
	public function getToString()
	{
		return $this->__toString();
	}

	/**
	 * Return array representation of this record
	 *
	 * @return array An array representation of the record.
	 */
	public function toDebug()
	{
		//    return Doctrine_Lib::getRecordAsString($this);
		return array(
      'state' => $this->state().'='.Doctrine_Lib::getRecordStateAsString($this->state()),
      'data' => $this->toArray()
		);
	}

	public function toArrayWithI18n($deep = true, $prefixKey = false)
	{
		$array = $this->toArray($deep, $prefixKey);

		if ($this->getTable()->hasI18n())
		{
			foreach($this->getTable()->getI18nTable()->getFieldNames() as $field)
			{
				$array[$field] = $this->get($field);
			}
		}

		return $array;
	}

	public function toIndexableString()
	{
		$index = '';

		foreach($this->_table->getIndexableColumns() as $columnName => $column)
		{
			$index .= ' '.$this->get($columnName);
		}

		return trim($index);
	}

	public function isFieldModified($field)
	{
		return array_key_exists($field, $this->getModified());
	}

	public function saveGet(Doctrine_Connection $conn = null)
	{
		$this->save($conn);

		return $this;
	}

	public function isNew()
	{
		if (parent::isNew())
		{
			return true;
		}

		if (!$this->_table instanceof dmDoctrineTable)
		{
			return false;
		}

		foreach($this->_table->getPrimaryKeys() as $pk)
		{
			if (!$this->get($pk))
			{
				return true;
			}
		}

		return false;
	}


	/**
	 * returns a value of a property or a related component
	 *
	 * @param mixed $fieldName                  name of the property or related component
	 * @param boolean $load                     whether or not to invoke the loading procedure
	 * @throws Doctrine_Record_Exception        if trying to get a value of unknown property / related component
	 * @return mixed
	 */
	public function get($fieldName, $load = true)
	{
		$hasAccessor = $this->hasAccessor($fieldName);

		if ($hasAccessor || $this->_table->getAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE))
		{
			$componentName = $this->_table->getComponentName();

			$accessor = $this->hasAccessor($fieldName)
			? $this->getAccessor($fieldName)
			: 'get' . dmString::camelize($fieldName);

			if ($hasAccessor || method_exists($this, $accessor))
			{
				/**
				 * Special case.
				 * For versionable tables, we don't want to use
				 * the getVersion accessor when requesting 'version'.
				 * This is because "Version" is a relation, and "version" is a fieldname.
				 * The case is lost when using getVersion.
				 */
				if ('getVersion' === $accessor && $this->getTable()->isVersionable())
				{
					return $this->_get($fieldName, $load);
				}

				/**
				 * Special case.
				 * ->getService() is reserved for getting services
				 */
				if ('getService' === $accessor)
				{
					return $this->_get($fieldName, $load);
				}

				$this->hasAccessor($fieldName, $accessor);
				return $this->$accessor($load);
			}
		}

		return $this->_get($fieldName, $load);
	}

	/**
	 * Pure overload without parent::_get
	 */
	public function _get($fieldName, $load = true)
	{
		$value = self::$_null;

		if (array_key_exists($fieldName, $this->_values)) {
			return $this->_values[$fieldName];
		}

		if (array_key_exists($fieldName, $this->_data)) {
			// check if the value is the Doctrine_Null object located in self::$_null)
			if ($this->_data[$fieldName] === self::$_null && $load) {
				$this->load();
			}

			if ($this->_data[$fieldName] === self::$_null) {
				$value = null;
			} else {
				$value = $this->_data[$fieldName];
			}

			return $value;
		}

		/*
		 * Add i18n capabilities
		 */
		if ($fieldName !== 'Translation' && $this->_table->hasI18n() && array_key_exists('id', $this->_data))
		{
			$i18nTable = $this->_table->getI18nTable();

			if ($i18nTable->hasField($fieldName))
			{
				return $this->_getI18n($fieldName, $load);
			}
			elseif(!ctype_lower($fieldName) && !ctype_upper($fieldName{0}))
			{
				$underscoredFieldName = dmString::underscore($fieldName);
				if (strpos($underscoredFieldName, '_') !== false && $i18nTable->hasField($underscoredFieldName))
				{
					return $this->_getI18n($underscoredFieldName, $load);
				}
			}
		}
		/*
		 * i18n end
		 */

		/*
		 * Allow to get values by modulized fieldName
		 * ex : _get('cssClass') returns _get('css_class')
		 */
		if(!ctype_lower($fieldName) && !ctype_upper($fieldName{0}) && !$this->contains($fieldName))
		{
			$underscoredFieldName = dmString::underscore($fieldName);
			if (strpos($underscoredFieldName, '_') !== false && $this->contains($underscoredFieldName))
			{
				return $this->_get($underscoredFieldName, $load);
			}
		}
		/*
		 * end
		 */

		try {
			if ( ! isset($this->_references[$fieldName]) && $load) {
				$rel = $this->_table->getRelation($fieldName);
				$this->_references[$fieldName] = $rel->fetchRelatedFor($this);
			}

			if ($this->_references[$fieldName] === self::$_null) {
				return null;
			}

			return $this->_references[$fieldName];
		} catch (Doctrine_Table_Exception $e) {
			$success = false;
			foreach ($this->_table->getFilters() as $filter) {
				try {
					$value = $filter->filterGet($this, $fieldName);
					$success = true;
				} catch (Doctrine_Exception $e) {}
			}
			if ($success) {
				return $value;
			} else {
				throw $e;
			}
		}
	}

	protected function _getNewI18n()
	{
		return $this->get('Translation')->get(sfConfig::get('sf_default_culture'));
	}

	public function _getI18n($fieldName, $load = true)
	{
		$culture = self::getDefaultCulture();

		$translation = $this->get('Translation');

		// we have a translation
		if($translation->contains($culture))
		{
			$i18n = $translation->get($culture);
		}
		// record is new so we use (or create) the fallback culture
		elseif($this->isNew())
		{
			$i18n = $translation->get(sfConfig::get('sf_default_culture'));
		}
		// record exists, try to fetch its missing translation
		else
		{
			$i18n = $this->_table->getI18nTable()->createQuery('t')
			->where('t.id = ?', $this->get('id'))
			->andWhere('t.lang = ?', $culture)
			->fetchRecord();

			// existing translation fetched
			if($i18n)
			{
				$translation->set($culture, $i18n);
			}
			// no translation for this culture, use fallback
			elseif($i18nFallback = $this->getI18nFallback())
			{
				$i18n = $i18nFallback;
			}
			// no fallback available
			else
			{
				return null;
			}
		}

		return $i18n->get($fieldName, $load);
	}

	public function getI18nFallback()
	{
		if (null !== $this->i18nFallback)
		{
			return $this->i18nFallback;
		}

		if ($this->isNew())
		{
			return null;
		}

		$i18nFallback = $this->_table->getI18nTable()->createQuery('t')
		->where('t.id = ?', $this->get('id'))
		->andWhere('t.lang = ?', sfConfig::get('sf_default_culture'))
		->fetchRecord();

		$this->i18nFallback = $i18nFallback ? $i18nFallback : false;

		return $this->i18nFallback;
	}

	/**
	 * Allow to set values by modulized fieldName
	 * ex : _get('cssClass') returns _get('css_class')
	 */
	public function _set($fieldName, $value, $load = true)
	{
		if(!ctype_lower($fieldName) && !ctype_upper($fieldName{0}) && !$this->contains($fieldName))
		{
			$underscoredFieldName = dmString::underscore($fieldName);
			if (strpos($underscoredFieldName, '_') !== false && $this->contains($underscoredFieldName))
			{
				return parent::_set($underscoredFieldName, $value, $load); //why it was not return'ing from here ?
				//calling parent::_set($fieldName, ...) will always causes throwing an error
			}
		}

		parent::_set($fieldName, $value, $load);
		
		return $this;
	}

	public function getEventDispatcher()
	{
		return $this->getTable()->getEventDispatcher();
	}

	public function getServiceContainer()
	{
		return $this->getTable()->getServiceContainer();
	}

	public function getService($name, $class = null)
	{
		return $this->getTable()->getService($name, $class);
	}

	public function getModuleManager()
	{
		return $this->getTable()->getModuleManager();
	}

	/**
	 * Hack to make Versionable behavior work with I18n tables
	 * it will add a where clause on the current culture
	 * to avoid selecting versions for all cultures.
	 */
	public function getVersion()
	{
		if (!$this->getTable()->isVersionable())
		{
			return $this->_get('version');
		}

		if (!$this->getTable()->hasI18n())
		{
			return $this->_get('Version');
		}

		return $this
		->getTable()
		->getI18nTable()
		->getTemplate('DmVersionable')
		->getPlugin()
		->getTable()
		->createQuery('v')
		->where('v.id = ?', $this->get('id'))
		->andWhere('v.lang = ?', self::getDefaultCulture())
		->fetchRecords();
	}

	/**
	 * Provides access to i18n methods
	 *
	 * @param  string $method    The method name
	 * @param  array  $arguments The method arguments
	 *
	 * @return mixed The returned value of the called method
	 */
	public function __call($method, $arguments)
	{
		try
		{
			return parent::__call($method, $arguments);
		}
		catch(Exception $parentException)
		{
			try
			{
				if ($this->getTable()->hasI18n() && ($i18n = $this->getCurrentTranslation()))
				{
					return call_user_func_array(array($i18n, $method), $arguments);
				}
			}
			catch (Exception $e) {}

			throw $parentException;
		}
	}


	public function setData(array $data)
	{
		$this->_data = $data;
	}

	/**
	 * dmMicroCache
	 */
	protected
	$cache;
	
	/**
	 * dmMicroCache
	 */
	protected static $classCache = array();

	protected function getCache($cacheKey, $default = null)
	{
		if(isset($this->cache[$cacheKey]))
		{
			return $this->cache[$cacheKey];
		}

		return $default;
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
	
	protected function getClassCache($cacheKey, $default = null)
	{
		if(isset(self::$classCache[$cacheKey]))
		{
			return self::$classCache[$cacheKey];
		}

		return $default;
	}

	protected function hasClassCache($cacheKey)
	{
		return isset(self::$classCache[$cacheKey]);
	}

	protected function setClassCache($cacheKey, $cacheValue)
	{
		return self::$classCache[$cacheKey] = $cacheValue;
	}

	public function clearClassCache($cacheKey = null)
	{
		if (null === $cacheKey)
		{
			self::$classCache = array();
		}
		elseif(isset(self::$classCache[$cacheKey]))
		{
			unset(self::$classCache[$cacheKey]);
		}

		return $this;
	}

	public function validate()
	{
		if(!$this instanceof DmPage) //DmPage does not need this as it is processed by SEO
		{
			$this->validateI18n();
		}
	}

	/**
	 * Validates I18n objects associated to $this
	 *
	 * @throws Doctrine_Validator_Exception
	 */
	public function validateI18n()
	{
		if($this->getTable()->hasI18n())
		{
			if($this->get('Translation')->count() === 0)
			{
				$newI18n = $this->_getNewI18n();
				if(!$newI18n->isValid())
				{
					throw new Doctrine_Validator_Exception(array($newI18n));
				}
			}
			else{
				$inError = array();

				foreach($this->get('Translation') as $translation)
				{
					$state = $translation->isValid();
					if(true !== $state)
					{
						$inError[] = $translation;
					}
				}
				if(!empty($inError))
				{
					throw new Doctrine_Validator_Exception($inError);
				}
			}
		}
	}
}