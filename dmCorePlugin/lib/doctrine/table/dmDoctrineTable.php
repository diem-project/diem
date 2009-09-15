<?php

abstract class dmDoctrineTable extends Doctrine_Table
{
  protected
  $hasI18n;

  /**
   * Construct template method.
   *
   * This method provides concrete Table classes with the possibility
   * to hook into the constructor procedure. It is called after the
   * Doctrine_Table construction process is finished.
   *
   * @return void
   */
  public function construct()
  {
    $this->hasI18n = $this->hasTemplate('Doctrine_Template_I18n');
    
    if ($this->hasI18n)
    {
      $this->unshiftFilter(new dmDoctrineRecordI18nFilter);
    }
  }
  /*
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
  /*
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
        if ($localModule = dmModuleManager::getModuleByModel($localRelation['class']))
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

  /*
   * @return myDoctrineRecord the first record in the table
   */
  public function findOne()
  {
    return $this->createQuery()->fetchRecord();
  }

  /*
   * Will join all record available medias
   * @return myDoctrineQuery
   */
  public function joinDmMedias(myDoctrineQuery $q)
  {
    foreach($this->getRelationHolder()->getLocalMedias() as $relation)
    {
      $q->withDmMedia($relation->getAlias());
    }

    return $q;
  }

  /*
   * Will join all localKey relations
   * @return myDoctrineQuery
   */
  public function joinLocals(myDoctrineQuery $q)
  {
    $rootAlias = $q->getRootAlias();

    foreach($this->getRelationHolder()->getLocals() as $relation)
    {
      $q->leftJoin(sprintf('%s.%s %s', $rootAlias, $relation->getAlias(), $relation->getAlias()));
    }

    return $q;
  }

  /*
   * Will join all relations
   * @return myDoctrineQuery
   */
  public function joinAll(myDoctrineQuery $q)
  {
    $rootAlias = $q->getRootAlias();

    foreach($this->getRelationHolder()->getAll() as $relation)
    {
      if ($relation['alias'] == 'Translation')
      {
        $q->withI18n();
      }
      elseif ($relation['class'] == 'DmMedia')
      {
        $q->leftJoin(sprintf('%s.%s %s', $rootAlias, $relation->getAlias(), $relation->getLocal()))
        ->leftJoin(sprintf('%s.%s %s', $relation->getLocal(), 'Folder', $relation->getLocal().'Folder'));
      }
      else
      {
        $q->leftJoin(sprintf('%s.%s %s', $rootAlias, $relation->getAlias(), $relation->getAlias()));
        $q->addOrderBy(sprintf('%s.%s %s', $relation->getAlias(), $relation->getTable()->getSortColumnName(), 'asc'));
      }
    }

    return $q;
  }

  /*
   * @return myDoctrine query
   * the default admin list query
   */
  public function getAdminListQuery(myDoctrineQuery $q)
  {
    return $this->joinAll($q);
  }

  /*
   * add i18n columns if needed
   */
  public function getAllColumns()
  {
    $columns = $this->getColumns();

    if($this->hasI18n)
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
    
    if ($this->hasI18n && $this->getI18nTable()->hasField($fieldName))
    {
      return true;
    }
  }


  /*
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
    foreach($columns as $columnName => $column)
    {
      if (!empty($column['autoincrement'])
      || in_array($columnName, array('created_at', 'updated_at')))
      {
        unset($columns[$columnName]);
      }
    }

    return $this->setCache('human_columns', $columns);
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
    return $this->hasTemplate('Sortable') && $this->getPrimaryKey() == 'id';
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

    return $this->setCache('i18n_table', $this->hasI18n
    ? $this->getRelationHolder()->get('Translation')->getTable()
    : false
    );
  }

  public function getDefaultQuery()
  {
    if ($this->hasCache('dm_default_query'))
    {
      return clone $this->getCache('dm_default_query');
    }

    $q = $this->createQuery('dm_query');

    if ($sortColumnName = $this->getSortColumnName())
    {
      $q->addOrderBy('dm_query.'.$sortColumnName);
    }
     
    return $this->setCache('dm_default_query', $q);
  }

  public function getSortColumnName()
  {
    return $this->getDefaultSortColumnName();
  }

  /*
   * Please override getSortColumnName instead
   */
  protected final function getDefaultSortColumnName()
  {
    if ($this->hasCache('dm_default_sort_column_name'))
    {
      return $this->getCache('dm_default_sort_column_name');
    }
    
    if ($this->isSortable())
    {
      #FIXME try to return SortableTemplate columnName instead of default position
      $columnName = 'position';
    }
    else
    {
      $columnName = $this->getIdentifierColumnName();
    }
    return $this->setCache('dm_default_sort_column_name', $columnName);
  }

  public function getIdentifierColumnName()
  {
    if ($this->hasCache('dm_identifier_column_name'))
    {
      return $this->getCache('dm_identifier_column_name');
    }

    if (!$columnName = dmArray::first(array_intersect(sfConfig::get('dm_orm_identifier_fields'), $this->getColumnNames())))
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

  /*
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


  /*
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
   * @param string peer class of the sortable objects
   * @param array id/position pairs
   * @param Connection an optional connection object
   *
   * @return Boolean true if the reordering took place, false if a database problem prevented it
   **/
  public function doSort($order)
  {
    if (!$this->hasField('position'))
    {
      throw new dmException(sprintf('%s table has no position field', $this->getComponentName()));
    }

    $records = $this->createQuery('q INDEXBY q.id')->whereIn('q.id', array_keys($order))->fetchRecords();

    foreach ($order as $id => $position)
    {
      $records[$id]->set('position', $position);
    }

    $records->save();
  }

  /*
   * return dmModule this record module
   */
  public function getDmModule()
  {
    if($this->hasCache('dm_module'))
    {
      return $this->getCache('dm_module');
    }

    return $this->setCache('dm_module', dmModuleManager::getModuleByModel($this->getComponentName()));
  }
  /*
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
}