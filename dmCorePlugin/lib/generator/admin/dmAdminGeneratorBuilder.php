<?php

/**
 * Create a generator.yml for an admin module
 */
class dmAdminGeneratorBuilder
{
  protected
  $module,
  $dispatcher,
  $table;

  public function __construct(dmModule $module, sfEventDispatcher $dispatcher)
  {
    $this->module = $module;
    $this->dispatcher = $dispatcher;
    $this->table = $module->getTable();
  }

  public function getTransformed($generator)
  {
    $yaml = sfYaml::load($generator);
    
    $config = $yaml['generator']['param']['config'];
    unset($yaml['generator']['param']['config']);
    $yaml['generator']['param']['i18n_catalogue'] = $this->module->getOption('i18n_catalogue', 'dm');

    $yaml['generator']['param']['config'] = $config;
    $yaml['generator']['param']['config'] = $this->getConfig();

    $transformed = sfYaml::dump($yaml, 6, 0);

    $transformed = preg_replace("|('~')|um", "~", $transformed);

    return $transformed;
  }

  protected function getConfig()
  {
    $config = array(
      'actions' => $this->getActions(),
      'fields'  => $this->getFields(),
      'list'    => $this->getList(),
      'filter'  => $this->getFilter(),
      'form'    => $this->getForm(),
      'edit'    => $this->getEdit(),
      'new'     => $this->getNew()
    );
    
    return $this->dispatcher->filter(
      new sfEvent($this, 'dm.admin_generator_builder.config', array('module' => $this->module)),
      $config
    )->getReturnValue();
  }

  protected function getActions()
  {
    return '~';
  }

  protected function getFields()
  {
    $fields = array();

    /*
     * Assign associated module name to association label
     */
    foreach($this->table->getRelationHolder()->getAssociations() as $alias => $relation)
    {
      if ($this->table->hasTemplate('DmGallery') && 'DmMedia' === $relation->getClass())
      {
        continue;
      }
      
      if ($relationModule = $this->module->getManager()->getModuleByModel($relation->getClass()))
      {
        $label = $relationModule->getPlural();
      }
      else
      {
        $label = dmString::humanize($alias);
      }
      
      $fields[dmString::underscore($alias).'_list'] = array(
        'label' => $label
      );
    }
    
    /*
     * Remove is_ prefix from boolean fields labels
     */
    foreach($this->getBooleanFields() as $booleanField)
    {
      if (strpos($booleanField, 'is_') === 0)
      {
        $fields[dmString::underscore($booleanField)] = array(
          'label' => dmString::humanize(preg_replace('|^is_(.+)$|', '$1', $booleanField))
        );
      }
    }
    
    if ($this->table->hasTemplate('DmGallery'))
    {
      $fields['dm_gallery'] = 'Gallery';
    }
    
    return $fields;
  }

  protected function getList()
  {
    return array(
      'display' => $this->getListDisplay(),
      'sort'    => $this->getListSort(),
      'table_method' => 'getAdminListQuery',
      'table_count_method' => '~',
      'sortable' => $this->table->isSortable() || $this->table->isNestedSet()
    );
  }

  protected function getListDisplay()
  {
    $display = array(
      '='.$this->table->getIdentifierColumnName()
    );

    $fields = dmArray::valueToKey(array_diff($this->table->getAllColumnNames(), array_unique(array_merge(
      // always exclude these fields
      $this->getListExcludedFields(),
      // already included
      array($this->table->getIdentifierColumnName()),
      // exlude primary keys
      $this->table->getPrimaryKeys(),
      // exclude collumn aggregation key fields
      array_keys((array)$this->table->getOption('inheritanceMap'))
    ))));
    
//    foreach($this->module->getDmMediaFields() as $mediaField)
//    {
//      $display[] = $mediaField.'_view_little';
//      unset($fields[$mediaFieldName]);
//    }

    if ($this->table->isNestedSet()) {
      $display[] = 'nested_set_indented_name';
      $display[] = 'nested_set_parent';
    }

    foreach($this->table->getRelationHolder()->getLocals() as $alias => $relation)
    {
      $display[] = $relation->getLocalColumnName();
      unset($fields[$relation->getLocalColumnName()]);
    }
  
    if ($this->table->hasTemplate('DmGallery'))
    {
      $display[] = 'dm_gallery';
    }

    foreach($this->table->getRelationHolder()->getForeigns() as $alias => $relation)
    {
      if ($relation->getType() !== Doctrine_Relation::ONE && $this->module->getManager()->getModuleByModel($relation->getClass()))
      {
        $display[] = dmString::underscore($alias).'_list';
      }
    }

    foreach($this->table->getRelationHolder()->getAssociations() as $alias => $relation)
    {
      if ($this->table->hasTemplate('DmGallery') && 'DmMedia' === $relation->getClass())
      {
        continue;
      }
      
      $display[] = dmString::underscore($alias).'_list';
    }

    foreach($this->table->getAllColumnNames() as $field)
    {
      if (in_array($field, $fields))
      {
        $display[] = $field;
        unset($fields[$field]);
      }
    }

    return $display;
  }

  protected function getListSort()
  {
    if ($this->table->hasColumn('position'))
    {
      $sort = array('position', 'asc');
    }
    elseif($this->table->hasColumn('created_at'))
    {
      $sort = array('created_at', 'desc');
    }
    else
    {
      $sort = array($this->table->getIdentifierColumnName(), 'asc');
    }
    return $sort;
  }

  protected function getFilter()
  {
    return array(
      'display' => $this->getFilterDisplay()
    );
  }

  protected function getFilterDisplay()
  {
    $display = array(
      $this->table->getIdentifierColumnName()
    );

    $fields = dmArray::valueToKey(array_diff($this->table->getAllColumnNames(), array_unique(array_merge(
      // always exclude these fields
      $this->getFilterExcludedFields(),
      // already included
      array($this->table->getIdentifierColumnName()),
      // exlude primary keys
      $this->table->getPrimaryKeys(),
      // exclude collumn aggregation key fields
      array_keys((array)$this->table->getOption('inheritanceMap'))
    ))));

    foreach($this->getBooleanFields() as $field)
    {
      if (in_array($field, $fields))
      {
        $display[] = $field;
        unset($fields[$field]);
      }
    }

    foreach($fields as $field)
    {
      $display[] = $field;
      unset($fields[$field]);
    }

    return $display;
  }

  protected function getForm()
  {
    return array(
      'display' => $this->getFormDisplay(),
      'class' => $this->module->getModel().'AdminForm',
      'fields' => $this->getFormFields()
    );
  }

  protected function getFormDisplay()
  {
    $fields = dmArray::valueToKey(array_diff($this->table->getAllColumnNames(), array_unique(array_merge(
      // always exclude these fields
      $this->getFormExcludedFields(),
      // exlude primary keys
      $this->table->getPrimaryKeys(),
      // exclude collumn aggregation key fields
      array_keys((array)$this->table->getOption('inheritanceMap'))
    ))));

    /*
     * Remove media fields not to see them in foreigns fields
     */
    foreach($this->table->getRelationHolder()->getLocalMedias() as $alias => $relation)
    {
      if (in_array($relation['local'], $fields))
      {
        unset($fields[$relation['local']]);
      }
    }

    $sets = array();

    $sets['NONE'] = array();

    if (in_array($this->table->getIdentifierColumnName(), $fields))
    {
      if('embed' != sfConfig::get('dm_i18n_form') || !$this->table->hasI18n() || !$this->table->isI18nColumn($field))
      {
        $sets['NONE'][] = $this->table->getIdentifierColumnName();
      }
      unset($fields[$this->table->getIdentifierColumnName()]);
    }

    foreach($this->getBooleanFields($fields) as $field)
    {
      if (in_array($field, $fields))
      {
        $sets['NONE'][] = $field;
        unset($fields[$field]);
      }
    }

    foreach($this->table->getRelationHolder()->getLocals() as $alias => $relation)
    {
      if ($relation->getClass() == 'DmMedia')
      {
        $sets[dmString::humanize($relation['local'])] = array(
          $relation['local'].'_form',
          $relation['local'].'_view'
        );
      }
      else
      {
        $sets['NONE'][] = $relation->getLocalColumnName();
        unset($fields[$relation->getLocalColumnName()]);
      }
    }

    if ($this->table->isNestedSet()) {
      $sets['NONE'][] = 'nested_set_parent_id';
    }

    foreach($this->getTextFields($fields) as $field)
    {
      if (in_array($field, $fields))
      {
        if('embed' != sfConfig::get('dm_i18n_form') || !$this->table->hasI18n() || !$this->table->isI18nColumn($field))
        {
          $sets[dmString::humanize($field)][] = $field;
        }
        unset($fields[$field]);
      }
    }

    foreach(array_merge(
      $this->table->getRelationHolder()->getAssociations(),
      $this->table->getRelationHolder()->getForeigns()
    ) as $alias => $relation)
    {
      if ($this->table->hasTemplate('DmGallery') && 'DmMedia' === $relation->getClass())
      {
        continue;
      }
      
      if ($relation->getType() !== Doctrine_Relation::ONE && $relationModule = $this->module->getManager()->getModuleByModel($relation->getClass()))
      {
        $label = $relationModule->getPlural();
      }
      else
      {
        continue;
      }
      
      $sets[$label][] = dmString::underscore($alias).'_list';
    }
    
    if ($this->table->hasTemplate('DmGallery'))
    {
      $sets['Gallery'][] = 'dm_gallery';
    }

    if('embed' == sfConfig::get('dm_i18n_form') && $this->table->hasI18n())
    {
      $sets['Lang'] = array();
      foreach(sfConfig::get('dm_i18n_cultures') as $culture)
      {
        $sets['Lang'][] = $culture;
      }
    }

    $sets['Others'] = array();

    foreach($fields as $field)
    {
      if('embed' != sfConfig::get('dm_i18n_form') || !$this->table->hasI18n() || !$this->table->isI18nColumn($field))
      {
        $sets['Others'][] = $field;
      }
      unset($fields[$field]);
    }
    
    return $this->removeEmptyValues($sets);
  }

  protected function getFormFields()
  {
    $fields = array();

    /*
     * Add is_link options
     */
    foreach($this->getStringFields() as $stringField)
    {
      if ($this->table->isLinkColumn($stringField))
      {
        $fields[dmString::underscore($stringField)] = array(
          'is_link' => true,
          'help'    => 'Drag & drop a page here from the PAGES panel, or write an url'
        );
      }
    }

    return $fields;
  }

  protected function getEdit()
  {
    return '~';
  }

  protected function getNew()
  {
    return '~';
  }

  protected function getTextFields($fields = null)
  {
    return $this->filterFields($fields, array('clob', 'blob'));
  }
  
  protected function getStringFields($fields = null)
  {
    return $this->filterFields($fields, array('string'));
  }

  protected function getBooleanFields($fields = null)
  {
    return $this->filterFields($fields, array('boolean'));
  }

  protected function filterFields($fields = null, $types)
  {
    $fields = null === $fields ? $this->table->getColumnNames() : $fields;

    foreach($fields as $key => $field)
    {
      if(!in_array(dmArray::get($this->table->getColumn($field), 'type'), $types))
      {
        unset($fields[$key]);
      }
    }

    return $fields;
  }

  protected function removeEmptyValues($values)
  {
    foreach($values as $key => $value)
    {
      if (empty($value))
      {
        unset($values[$key]);
      }
    }
    return $values;
  }

  protected function getListExcludedFields()
  {
    $fields = array();

    if($this->table->hasI18n())
    {
      $fields[] = 'lang';
    }
    if($this->table->isVersionable())
    {
      $fields[] = 'version';
    }
    if($this->table->isSortable())
    {
      $fields[] = 'position';
    }
    if ($this->table->isNestedSet()) {
      $fields[] = 'lft';
      $fields[] = 'rgt';
      $fields[] = 'level';
      if ($this->table->getTemplate('NestedSet')->getOption('hasManyRoots')) {
        $fields[] = $this->table->getTemplate('NestedSet')->getOption('rootColumnName', 'root_id');
      }
    }

    return $fields;
  }

  protected function getFormExcludedFields()
  {
    $fields = array('created_at', 'updated_at');

    if($this->table->hasI18n())
    {
      $fields[] = 'lang';
    }
    if($this->table->isVersionable())
    {
      $fields[] = 'version';
    }
    if($this->table->isSortable())
    {
      $fields[] = 'position';
    }
    if ($this->table->isNestedSet()) {
      $fields[] = 'lft';
      $fields[] = 'rgt';
      $fields[] = 'level';
      if ($this->table->getTemplate('NestedSet')->getOption('hasManyRoots')) {
        $fields[] = $this->table->getTemplate('NestedSet')->getOption('rootColumnName', 'root_id');
      }
    }

    return $fields;
  }

  protected function getFilterExcludedFields()
  {
    return $this->getListExcludedFields();
  }

}