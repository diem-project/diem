<?php

class dmAdminDoctrineGenerator extends sfDoctrineGenerator
{

	protected
	$module;
	
  /**
   * Returns the default configuration for fields.
   *
   * @return array An array of default configuration for all fields
   */
  public function getDefaultFieldsConfiguration()
  {
    $fields = array();

    $names = array();
    foreach ($this->getColumns() as $name => $column)
    {
      $names[] = $name;

      $label = dmString::humanize($name);
      if ($localRelation = $this->table->getRelationHolder()->getLocalByColumnName($name))
      {
        if ($module = dmModuleManager::getModuleByModel($localRelation->getClass()))
        {
        	if ($module->isProject())
        	{
        		$label = $module->getName();
        	}
        }
      }
      $fields[$name] = array_merge(array(
        'is_link'      => (boolean) $column->isPrimaryKey(),
        'is_real'      => true,
        'is_partial'   => false,
        'is_component' => false,
        'type'         => $this->getType($column),
        'markdown'     => strpos($column->getDefinitionKey('extra'), 'markdown') !== false,
        'label'        => $label
      ), isset($this->config['fields'][$name]) ? $this->config['fields'][$name] : array());
    }

    foreach ($this->getManyToManyTables() as $relation)
    {
      $name = dmString::underscore($relation['alias']).'_list';
      $names[] = $name;
      $module = dmModuleManager::getModuleByModel($relation->getClass());
      $fields[$name] = array_merge(array(
        'is_link'      => false,
        'is_real'      => false,
        'is_partial'   => false,
        'is_component' => false,
        'type'         => 'Text',
        'label'        => $module ? $module->getPlural() : dmString::humanize($alias)
      ), isset($this->config['fields'][$name]) ? $this->config['fields'][$name] : array());
    }

    foreach ($this->table->getRelationHolder()->getForeigns() as $alias => $relation)
    {
      if (dmModuleManager::getModuleByModel($relation->getClass()))
      {
	      $name = dmString::underscore($alias).'_list';
	      $names[] = $name;
	      $fields[$name] = array_merge(array(
	        'is_link'      => false,
	        'is_real'      => false,
	        'is_partial'   => false,
	        'is_component' => false,
	        'type'         => 'Text',
	        'label'        => dmModuleManager::getModule($relation->getClass())->getPlural()
	      ), isset($this->config['fields'][$name]) ? $this->config['fields'][$name] : array());
      }
    }

    if (isset($this->config['fields']))
    {
      foreach ($this->config['fields'] as $name => $params)
      {
        if (in_array($name, $names))
        {
          continue;
        }

        $fields[$name] = array_merge(array(
          'is_link'      => false,
          'is_real'      => false,
          'is_partial'   => false,
          'is_component' => false,
          'type'         => 'Text',
        ), is_array($params) ? $params : array());
      }
    }

    unset($this->config['fields']);

    return $fields;
  }

  /**
   * Returns HTML code for a field.
   *
   * @param sfModelGeneratorConfigurationField $field The field
   *
   * @return string HTML code
   */
  public function renderField($field)
  {
  	$fieldName = $field->getName();

    $html = $this->getColumnGetter($fieldName, true);

    if ($renderer = $field->getRenderer())
    {
      $html = sprintf('%s ? call_user_func_array(%s, array_merge(array(%s), %s)) : "&nbsp;"', $html, $this->asPhp($renderer), $html, $this->asPhp($field->getRendererArguments()));
    }
    else if ($field->isComponent())
    {
      return sprintf("get_component('%s', '%s', array('type' => 'list', '%s' => \$%s))", $this->getModuleName(), $fieldName, $this->getSingularName(), $this->getSingularName());
    }
    else if ($field->isPartial())
    {
      return sprintf("get_partial('%s', array('type' => 'list', '%s' => \$%s))", $fieldName, $this->getSingularName(), $this->getSingularName());
    }
    else if ('Date' == $field->getType())
    {
      $html = sprintf("false !== strtotime($html) ? format_date(%s, \"%s\") : '&nbsp;'", $html, $field->getConfig('date_format', 'f'));
    }
    else if ('Boolean' == $field->getType())
    {
      $html = sprintf("get_partial('%s/list_field_boolean', array('value' => %s))", $this->getModuleName(), $html);
    }
    /*
     * Local Relation
     */
    elseif($relation = $this->table->getRelationHolder()->getLocalByColumnName($fieldName))
    {
	    if ($relation->getClass() === 'DmMedia')
	    {
	      $html = '$'.$this->getSingularName().'->'.$relation->getLocalColumnName().' ? get_partial("dmMedia/viewLittle", array("object" => $'.$this->getSingularName().'->'.$relation->getAlias().')) : "-"';
	    }
	    else
	    {
	      $html = '$'.$this->getSingularName().'->'.$relation->getLocalColumnName().' ? $'.$this->getSingularName().'->'.$relation->getAlias().' : "-"';
	    }
    }
    /*
     * Foreign or Association Relation
     */
    elseif(substr($fieldName, -5) === '_list')
    {
      if (!$relation = $this->table->getRelationHolder()->get($alias = dmString::camelize(substr($fieldName, 0, strlen($fieldName)-5))))
      {
      	$relation = $this->table->getRelationHolder()->get($alias = substr($fieldName, 0, strlen($fieldName)-5));
      }
      if ($relation)
      {
	      if($relation instanceof Doctrine_Relation_ForeignKey)
	      {
	        $html = 'get_partial("dmAdminGenerator/relationForeign", array("record" => $'.$this->getSingularName().', "alias" => "'.$alias.'"));';
	      }
		    elseif ($relation instanceof Doctrine_Relation_Association)
		    {
		      $html = 'get_partial("dmAdminGenerator/relationAssociation", array("record" => $'.$this->getSingularName().', "alias" => "'.$alias.'"));';
		    }
      }
    }
    else
    {
      $html = 'dmString::truncate('.$html.', '.$field->getConfig('truncate', sfConfig::get('dm_admin_list_truncate', 120)).')';
    }

    if ($field->isLink())
    {
      $html = sprintf("link_to(%s, '%s', \$%s)", $html, $this->getUrlForAction('edit'), $this->getSingularName());
    }

    return $html;
  }

	public function getColumns()
  {
    return $this->table->getSfDoctrineColumns();
  }

	/**
   * Returns HTML code for an action link.
   *
   * @param string  $actionName The action name
   * @param array   $params     The parameters
   * @param boolean $pk_link    Whether to add a primary key link or not
   *
   * @return string HTML code
   */
  public function getLinkToAction($actionName, $params, $pk_link = false)
  {
    $action = isset($params['action']) ? $params['action'] : dmString::modulize($actionName);

    $url_params = $pk_link ? '?'.$this->getPrimaryKeyUrlParams() : '\'';

    return '[?php echo link_to(__(\''.dmArray::get($params, 'label', dmString::humanize($actionName)).'\', array(), \''.$this->getI18nCatalogue().'\'), \''.$this->getModuleName().'/'.$action.$url_params.', '.$this->asPhp($params['params']).') ?]';
  }

  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);

    $this->setGeneratorClass('dmAdminDoctrineModule');
  }

  public function getModule()
  {
  	if ($this->module === null)
  	{
  		$this->module = dmModuleManager::getModuleOrNull($this->getModuleName());
  	}

    return $this->module;
  }

  /**
   * Gets the i18n catalogue to use for user strings.
   *
   * @return string The i18n catalogue
   */
  public function getI18nCatalogue()
  {
    return sfConfig::get('dm_i18n_catalogue');
  }

}