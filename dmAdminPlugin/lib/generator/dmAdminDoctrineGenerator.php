<?php

class dmAdminDoctrineGenerator extends sfDoctrineGenerator
{

  protected
  $module,
  $moduleManager;
  
  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);
    
    $this->moduleManager = dmContext::getInstance()->getModuleManager();

    $this->setGeneratorClass('dmAdminDoctrineModule');
  }

  /**
   * Configures this generator.
   */
  public function configure()
  {
    parent::configure();

    $this->generatorManager->getConfiguration()->getEventDispatcher()->notify(
      new sfEvent($this, 'dm.admin_generator.post_configure', array('table' => $this->table))
    );
  }
  
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

      if ($localRelation = $this->table->getRelationHolder()->getLocalByColumnName($name))
      {
        $label = dmString::humanize($localRelation->getAlias());
      }
      else
      {
        $label = dmString::humanize($name);
      }
      $fields[$name] = array_merge(array(
        'is_link'      => (boolean) $column->isPrimaryKey(),
        'is_real'      => true,
        'is_partial'   => false,
        'is_component' => false,
        'type'         => $this->getType($column),
        'markdown'     => $this->module->getTable()->isMarkdownColumn($name),
        'label'        => $label
      ), isset($this->config['fields'][$name]) ? $this->config['fields'][$name] : array());
    }

    foreach ($this->getManyToManyTables() as $relation)
    {
      $name = dmString::underscore($relation->getAlias()).'_list';
      $names[] = $name;
      $module = $this->moduleManager->getModuleByModel($relation->getClass());
      $fields[$name] = array_merge(array(
        'is_link'      => false,
        'is_real'      => false,
        'is_partial'   => false,
        'is_component' => false,
        'type'         => 'Text',
        'label'        => $module ? $module->getPlural() : dmString::humanize($relation->getAlias())
      ), isset($this->config['fields'][$name]) ? $this->config['fields'][$name] : array());
    }

    foreach ($this->table->getRelationHolder()->getForeigns() as $alias => $relation)
    {
      if ($this->moduleManager->getModuleByModel($relation->getClass()))
      {
        $name = dmString::underscore($alias).'_list';
        $names[] = $name;
        $fields[$name] = array_merge(array(
          'is_link'      => false,
          'is_real'      => false,
          'is_partial'   => false,
          'is_component' => false,
          'type'         => 'Text',
          'label'        => $this->moduleManager->getModuleByModel($relation->getClass())->getPlural()
        ), isset($this->config['fields'][$name]) ? $this->config['fields'][$name] : array());
      }
    }

    foreach($this->table->getRelationHolder()->getLocalMedias() as $alias => $relation)
    {
      $name = $relation->getLocal().'_form';
      $names[] = $name;
      $fields[$name] = array_merge(array(
        'label'        => $alias,
        'is_real'      => false,
        'type'         => 'Text'
      ), isset($this->config['fields'][$name]) ? $this->config['fields'][$name] : array());
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
      return sprintf("get_component('%s', '%s', array('type' => 'list', 'helper' => \$helper, 'security_manager' => \$security_manager, '%s' => \$%s))", $this->getModuleName(), $fieldName, $this->getSingularName(), $this->getSingularName());
    }
    else if ($field->isPartial())
    {
      return sprintf("get_partial('%s/%s', array('type' => 'list', 'helper' => \$helper, 'security_manager' => \$security_manager, '%s' => \$%s))", $this->getModuleName(), $fieldName, $this->getSingularName(), $this->getSingularName());
    }
    else if ('Date' == $field->getType())
    {
      $html = sprintf("false !== strtotime($html) ? format_date(%s, \"%s\") : '&nbsp;'", $html, $field->getConfig('date_format', 'f'));
    }
    else if ('Boolean' == $field->getType())
    {
      $html = "sprintf('<a class=\"s16block s16_%s {field: \'%s\'}\" title=\"%s\"></a>', ".$html." ? 'tick' : 'cross', '".$fieldName."', __('Click to edit'))";
    }
    /*
     * Local Relation
     */
    elseif($relation = $this->table->getRelationHolder()->getLocalByColumnName($fieldName))
    {
      if ('DmMedia' === $relation->getClass())
      {
        $html = '$'.$this->getSingularName()."->get('".$relation->getLocalColumnName()."') ? get_partial('dmMedia/viewLittle', array('object' => $".$this->getSingularName()."->get('".$relation->getAlias()."'))) : '-'";
      }
      else
      {
        $localModule = $this->moduleManager->getModuleByModel($relation->getClass());
        
        if ($localModule && $localModule->hasAdmin())
        {
          $html = "(\$sf_user->canAccessToModule('{$localModule->getKey()}')
? _link(\${$this->getSingularName()}->get('{$relation->getAlias()}'))
->text(\${$this->getSingularName()}->get('{$relation->getAlias()}')->__toString())
->set('.associated_record.s16right.s16_arrow_up_right_medium')
: $".$this->getSingularName()."->get('".$relation->getAlias()."'))";
        }
        else
        {
          $html = "$".$this->getSingularName()."->get('".$relation->getAlias()."')";
        }
        
        $html = '$'.$this->getSingularName()."->get('".$relation->getLocalColumnName()."') ? ".$html." : '-'";
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
        $html = "\$sf_context->getServiceContainer()->mergeParameter('related_records_view.options', array(
  'record' => $".$this->getSingularName().",
  'alias'  => '".$alias."'
))->getService('related_records_view')->render()";
      }
    }
    elseif ('dm_gallery' === $fieldName)
    {
      $html = "get_partial('dmMedia/galleryLittle', array('record' => $".$this->getSingularName()."));";
    }
    else
    {
      $html = 'htmlentities(dmString::truncate('.$html.', '.$field->getConfig('truncate', sfConfig::get('dm_admin_list_truncate', 120)).'), ENT_COMPAT, \'UTF-8\')';
      
      if ($this->module->getTable()->isMarkdownColumn($fieldName))
      {
        $html = "str_replace(array('*', '#'), '', ".$html.")";
      }
    }

    if ($field->isLink())
    {
      $html = sprintf("\$security_manager->userHasCredentials('edit', \$%s) ? _link('@%s?action=edit&pk='.\$%s->getPrimaryKey())->text(%s)->addClass('link_edit') : (%s)", $this->getSingularName(), $this->module->getUnderscore(), $this->getSingularName(), $html, $html);
    }

    return $html;
  }
  
  /**
   * Returns the getter either non-developped: 'getFoo' or developped: '$class->getFoo()'.
   *
   * @param string  $column     The column name
   * @param boolean $developed  true if you want developped method names, false otherwise
   * @param string  $prefix     The prefix value
   *
   * @return string PHP code
   */
  public function getColumnGetter($column, $developed = false, $prefix = '')
  {
    $getter = 'get(\''.$column.'\')';
    if ($developed)
    {
      $getter = sprintf('$%s%s->%s', $prefix, $this->getSingularName(), $getter);
    }

    return $getter;
  }
  
  /**
   * Returns PHP code to add to a URL for primary keys.
   *
   * @param string $prefix The prefix value
   *
   * @return string PHP code
   */
  public function getPrimaryKeyUrlParams($prefix = '', $full = false)
  {
    $params = array();
    foreach ($this->getPrimaryKeys() as $pk)
    {
      $fieldName = sfInflector::underscore($pk);

      if ($full)
      {
        $params[] = sprintf("%s='.%s->%s", $fieldName, $prefix, $this->getColumnGetter($fieldName, false));
      }
      else
      {
        $params[] = sprintf("%s='.%s", $fieldName, $this->getColumnGetter($fieldName, true, $prefix));
      }
    }

    return implode(".'&", $params);
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
   * @return dmModule 
   */
  public function getModule()
  {
    if ($this->module === null)
    {
      $this->module = $this->moduleManager->getModuleBySfName($this->getModuleName());
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
    return $this->getModule()->getOption('i18n_catalogue', sfConfig::get('dm_i18n_catalogue'));
  }
  
  /**
   * Gets the actions base class for the generated module.
   *
   * @return string The actions base class
   */
  public function getActionsBaseClass()
  {
    return isset($this->params['actions_base_class']) ? $this->params['actions_base_class'] : 'myAdminBaseGeneratedModuleActions';
  }
  
  public function addCredentialCondition($content, $params = array(), $action=null)
  {
    if(null === $action){ 
      return parent::addCredentialCondition($content, $params);
    }
  }
}