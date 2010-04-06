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
  
  /**
   * Returns a sfWidgetForm class name for a given column.
   *
   * @param  sfDoctrineColumn $column
   * @return string    The name of a subclass of sfWidgetForm
   */
  public function getWidgetClassForColumn($column)
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

    return $class;
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
        $options[] = "'choices' => " . str_replace("\n", '', $this->arrayExport($values));
        break;
    }

    if ($column->isForeignKey())
    {
      $options[] = sprintf('\'model\' => \'%s\', \'add_empty\' => true', $column->getForeignTable()->getOption('name'));
    }

    return count($options) ? sprintf('array(%s)', implode(', ', $options)) : '';
  }
  
  /**
   * Returns a sfValidator class name for a given column.
   *
   * @param  sfDoctrineColumn $column
   * @return string    The name of a subclass of sfValidator
   */
  public function getValidatorClassForColumn($column)
  {
    $class = parent::getValidatorClassForColumn($column);

    if('sfValidatorDateRange' == $class)
    {
      $class = 'sfValidatorChoice';
    }

    return $class;
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

    if(in_array($column->getDoctrineType(), array('date', 'datetime', 'timestamp')))
    {
      $options = "array('required' => false, 'choices' => array_keys(\$this->widgetSchema['{$column->getName()}']->getOption('choices')))";
    }

    return $options;
  }
}
