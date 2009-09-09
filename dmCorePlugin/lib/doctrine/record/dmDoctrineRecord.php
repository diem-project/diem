<?php

abstract class dmDoctrineRecord extends sfDoctrineRecord
{

  /**
   * Custom myDoctrineRecord constructor.
   * Used to initialize I18n to make sure the culture is set from symfony
   *
   * @return void
   */
  public function construct()
  {
    if ($this->_table->hasI18n())
    {
      self::initializeI18n();
    }
  }

  /*
   * Add page tree watcher registering
   */
  public function preSave($event)
  {
    parent::preSave($event);

    if ($this->isModified())
    {
      $this->notifyPageTreeWatcher();
    }
  }

  /*
   * Add page tree watcher registering
   */
  public function postDelete($event)
  {
    parent::postDelete($event);

    $this->notifyPageTreeWatcher();
  }

  /*
   * Add page tree watcher registering
   */
  public function unlinkInDb($alias, $ids = array())
  {
    $this->notifyPageTreeWatcher();

    return parent::unlinkInDb($alias, $ids);
  }

  public function notifyPageTreeWatcher()
  {
    if ($this->_table instanceof dmDoctrineTable && $this->_table->interactsWithPageTree())
    {
      dmContext::getInstance()->getPageTreeWatcher()->addModifiedTable($this->_table);
    }
  }

  public function refresh($deep = false)
  {
    $return = parent::refresh($deep);
    $this->clearCache();
    return $return;
  }

  /*
   * Will try to randomly fill empty record field according to their type
   * It may fail in some case.
   */
  public function loremize()
  {
    return dmRecordLoremizer::loremize($this);
  }

  /*
   * Tries to return the nearest records in table
   */
  public function getNearRecords(Doctrine_Query $q, $nb = 11)
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
    $pks = $qPk->select($qPk->getRootAlias().'.'.$pk)->execute(array(), Doctrine::HYDRATE_SCALAR);

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

    return $rPk->whereIn($rPk->getRootAlias().'.'.$pk, $selectedPks)->execute(array(), Doctrine::HYDRATE_RECORD);
  }

  /*
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

  /*
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

    if (!isset($myOffset) || $myOffset === (count($nearRecords)-1))
    {
      return null;
    }

    return $nearRecords[$myOffset+1];
  }

  /*
   * add fluent interface to @see parent::fromArray
   * @return myDoctrineRecord $this ( fluent interface )
   */
  public function fromArray(array $array, $deep = true)
  {
    parent::fromArray($array, $deep);
    return $this;
  }

  /*
   * @return DmMediaFolder the DmMediaFolder used to store this record's medias
   */
  public function getDmMediaFolder()
  {
    return $this->_table->getDmMediaFolder();
  }

  /*
   * @return DmMedia the associated media for this columnName or null
   */
  public function getDmMediaByColumnName($columnName)
  {
    $relation = $this->_table->getRelationHolder()->getLocalByColumnName($columnName);

    if (!$relation instanceof Doctrine_Relation_LocalKey)
    {
      throw new dmException(sprintf('%s is not a DmMedia LocalKey columnName : %s', $columnName, get_class($relation)));
    }

    if(!$media = $this->get($relation['alias'])->orNull())
    {
      if($media = dmDb::table('DmMedia')->findOneByIdWithFolder($this->get($columnName)))
      {
        $this->set($relation['alias'], $media);
      }
    }

    return $media;
    //    return $relation->fetchRelatedFor($this);
  }

  /*
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

  /*
   * @return dmModule this record module
   */
  public function getDmModule()
  {
    return $this->_table->getDmModule();
  }

  /*
   * @return dmPage this record page
   */
  public function getDmPage()
  {
    if(!$this->getDmModule()->hasPage())
    {
      throw new dmException(sprintf('record %s has no page because module %s has no page', get_class($this), $this->getDmModule()));
    }

    return dmDb::table('DmPage')->findOneByRecord($this);
  }

  /*
   * Will find in module ancestors the requested model
   * and return the ancestor record for this model
   * @return myDoctrineRecord the ancestor record
   */
  public function getAncestorRecord($class, $hydrationMode = Doctrine::HYDRATE_RECORD)
  {
    if (get_class($this) == $class)
    {
      return $this;
    }

    $module = $this->getDmModule();
    $ancestorKey = dmString::modulize($class);

    if(!$ancestorModule = $module->getAncestor($class))
    {
      throw new dmRecordException(sprintf('%s is not an ancestor of %s', $ancestorKey, $module));
      return null;
    }

    return dmDb::query($ancestorModule->getModel().' '.$ancestorModule->getKey())
    ->whereDescendantId(get_class($this), $this->id, $ancestorModule->getModel())
    ->fetchRecord();

    //    $ancestorRecord = $this;
    //    foreach(array_reverse($module->getPath()) as $aModule)
    //    {
    //      /*
    //       * Found ancestor
    //       */
    //      if($aModule->getModel() == $ancestorModule->getModel())
    //      {
    //        return $ancestorRecord->getRelatedRecord($aModule->getModel(), $hydrationMode);
    //      }
    //
    //      /*
    //       * Record ancestor chain terminated.
    //       */
    //      if(!$ancestorRecord = $ancestorRecord->getRelatedRecord($aModule->getModel()))
    //      {
    //        return null;
    //      }
    //    }
    //
    //    return null;
  }

  /*
   * Will find in module ancestors the requested model
   * and return the ancestor record id for this model
   * @return int the ancestor record id
   */
  public function getAncestorRecordId($class)
  {
    if (get_class($this) == $class)
    {
      return $this->id;
    }

    $module = $this->getDmModule();
    $ancestorKey = dmString::modulize($class);

    if(!$ancestorModule = $module->getAncestor($ancestorKey))
    {
      throw new dmRecordException(sprintf('%s is not an ancestor of %s', $ancestorKey, $module));
      return null;
    }

    return dmDb::query($ancestorModule->getModel().' '.$ancestorModule->getKey())
    ->whereDescendantId($module->getModel(), $this->id, $ancestorModule->getModel())
    ->select($ancestorModule->getKey().'.id')
    ->fetchValue();
    //
    //    $ancestorRecord = $this;
    //    foreach(array_reverse($module->getPath()) as $aModule)
    //    {
    //      /*
    //       * Found ancestor
    //       */
    //      if($aModule->getModel() == $ancestorModule->getModel())
    //      {
    //        return $ancestorRecord->getRelatedRecordId($aModule->getModel());
    //      }
    //
    //      /*
    //       * Record ancestor chain terminated.
    //       */
    //      if(!$ancestorRecord = $ancestorRecord->getRelatedRecord($aModule->getModel()))
    //      {
    //        return null;
    //      }
    //    }
    //
    //    return null;
  }

  /*
   * Returns one record related to this one by $alias
   * LocalKey relation : the related record
   * ForeignKey && Association relation : the first related record
   * @return myDoctrineRecord|null the related record or null if not exist
   */
  public function getRelatedRecord($class, $hydrationMode = Doctrine::HYDRATE_RECORD)
  {
    if (!$relation = $this->_table->getRelationHolder()->getByClass($class))
    {
      throw new dmRecordException(sprintf('%s has no relation for class %s', get_class($this), $class));
      return null;
    }

    if ($relation instanceof Doctrine_Relation_LocalKey)
    {
      return $relation['table']->createQuery('foreign')
      ->where('foreign.id  = ?', $this->get($relation['local']))
      ->dmCache()
      ->fetchRecord(array(), $hydrationMode);
    }
    elseif($relation instanceof Doctrine_Relation_ForeignKey)
    {
      return $relation['table']->createQuery('foreign')
      ->where('foreign.'.$relation->getForeignColumnName().' = ?', $this->id)
      ->dmCache()
      ->fetchRecord(array(), $hydrationMode);
    }
    elseif($relation instanceof Doctrine_Relation_Association)
    {
      return $relation['table']->createQuery('foreign')
      ->leftJoin('foreign.'.$relation['refTable']->getComponentName().' ref_table')
      ->where('ref_table.'.$relation['local'].' = ?', $this->id)
      ->dmCache()
      ->fetchRecord(array(), $hydrationMode);
    }
    else
    {
      throw new dmException('Strange relation...');
    }
  }

  /*
   * Returns one record related id to this one by $alias
   * LocalKey relation : the related record id
   * ForeignKey && Association relation : the first related record id
   * @return int|null the related record id or null if not exist
   */
  public function getRelatedRecordId($class)
  {
    if (!$relation = $this->_table->getRelationHolder()->getByClass($class))
    {
      throw new dmRecordException(sprintf('%s has no relation for class %s', get_class($this), $class));
      return null;
    }

    if ($relation instanceof Doctrine_Relation_LocalKey)
    {
      return $this->get($relation['local']);
    }
    elseif($relation instanceof Doctrine_Relation_ForeignKey)
    {
      return $relation['table']->createQuery('foreign')
      ->select('foreign.id')
      ->where('foreign.'.$relation->getForeignColumnName().' = ?', $this->id)
      ->limit(1)
      ->dmCache()
      ->fetchValue();
    }
    elseif($relation instanceof Doctrine_Relation_Association)
    {
      return $relation->getAssociationTable()->createQuery('association')
      ->select('association.'.$relation['foreign'])
      ->where('association.'.$relation['local'].' = ?', $this->id)
      ->limit(1)
      ->dmCache()
      ->fetchValue();
    }
    else
    {
      throw new dmException('Strange relation...');
    }
  }

  /*
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
    $guesses = sfConfig::get('dm_orm_identifier_fields');

    // we try to guess a column which would give a good description of the object
    foreach ($guesses as $descriptionColumn)
    {
      try
      {
        return (string) $this->get($descriptionColumn);
      }
      catch (Exception $e)
      {
        // Try another one
      }
    }

    return sprintf('No description for object of class "%s"', $this->_table->getComponentName());
  }

  /*
   * Shortcut to __toString method
   * used by admin generator
   */
  public function getToString()
  {
    return $this->__toString();
  }

  /*
   * Return array representation of this record
   *
   * @return array An array representation of the record.
   */
  public function toDebug()
  {
    //  	return Doctrine_Lib::getRecordAsString($this);
    return array(
      'state' => $this->state().'='.Doctrine_Lib::getRecordStateAsString($this->state()),
      'data' => $this->toArray()
    );
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


  /*
   * Pure overload without parent::_get
   */
  public function _get($fieldName, $load = true)
  {
    if (isset($this->_values[$fieldName])) {
      return $this->_values[$fieldName];
    }

    if (isset($this->_data[$fieldName])) {
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
    if ($fieldName != 'Translation' && $this->_table->hasI18n())
    {
      $i18nTable = $this->_table->getI18nTable();

      if ($i18nTable->hasField($fieldName))
      {
        return $this->_getI18n($fieldName, $load);
      }
      elseif(!ctype_lower($fieldName))
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

  public function _getI18n($fieldName, $load = true)
  {
    $culture = myDoctrineRecord::getDefaultCulture();

    $translation = $this->get('Translation');

    if (isset($translation[$culture]))
    {
      $i18n = $translation[$culture];
    }
    else
    {
      $i18n = $translation[sfConfig::get('sf_default_culture')];
    }

    return $i18n->get($fieldName, $load);
  }

  /*
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
        return parent::_set($underscoredFieldName, $value, $load);
      }
    }

    return parent::_set($fieldName, $value, $load);
  }

  /*
   * dmMicroCache
   */

  private
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

  protected function clearCache($cacheKey = null)
  {
    if (is_null($cacheKey))
    {
      $this->cache = array();
    }
    elseif(isset($this->cache[$cacheKey]))
    {
      unset($this->cache[$cacheKey]);
    }

    return $this;
  }
}