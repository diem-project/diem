<?php

class dmDoctrineFormFilterGenerator extends sfDoctrineFormFilterGenerator
{

  /**
   * Filter out models that have disabled generation of form classes
   *
   * @return array $models Array of models to generate forms for
   */
  protected function filterModels($models)
  {
    $models = parent::filterModels($models);
    
    foreach ($models as $key => $model)
    {
      if (strncmp($model, 'ToPrfx', 6) === 0)
      {
        unset($models[$key]);
      }
    }

    return $models;
  }

  public function getWidgetOptionsForColumn($column)
  {
    $options = array();

    $withEmpty = sprintf('\'with_empty\' => %s', $column->isNotNull() ? 'false' : 'true');
    switch ($column->getDoctrineType())
    {
      case 'boolean':
        $options[] = "'choices' => array('' => dm::getI18n()->__('yes or no', array(), 'dm'), 1 => dm::getI18n()->__('yes', array(), 'dm'), 0 => dm::getI18n()->__('no', array(), 'dm'))";
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
