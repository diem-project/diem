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
      $class = 'sfWidgetFormDmFilterDate';
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
        $widget = 'new sfWidgetFormInputText(array(), array("class" => "datepicker_me"))';
        $options[] = "'from_date' => $widget, 'to_date' => $widget";
        $options[] = $withEmpty;
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

  
}
