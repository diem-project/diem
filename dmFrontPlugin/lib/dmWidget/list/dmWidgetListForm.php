<?php

class dmWidgetListForm extends dmWidgetProjectModelForm
{
  protected
  $firstDefaults = array(
    'orderType'  => 'asc',
    'maxPerPage' => 5
  );

  public function configure()
  {
    parent::configure();

    /*
     * Max per page
     */
    $this->widgetSchema['maxPerPage']     = new sfWidgetFormInputText(array(), array(
      'size' => 3
    ));
    $this->validatorSchema['maxPerPage']  = new sfValidatorInteger(array(
      'min' => 0,
      'max' => 9999
    ));

    /*
     * Paginators top & bottom
     */
    $this->widgetSchema['navTop']      = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['navTop']   = new sfValidatorBoolean();

    $this->widgetSchema['navBottom']    = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['navBottom'] = new sfValidatorBoolean();

    /*
     * Order field selection
     */
    $orderFields = $this->getAvailableOrderFields();
    $this->widgetSchema['orderField']    = new sfWidgetFormSelect(array(
      'choices' => $orderFields
    ));
    $this->validatorSchema['orderField'] = new sfValidatorChoice(array(
      'choices' => array_keys($orderFields)
    ));

    /*
     * Order type selection
     */
    $orderTypes = $this->getOrderTypes();
    $this->widgetSchema['orderType']    = new sfWidgetFormSelect(array(
      'choices' => $orderTypes
    ));
    $this->validatorSchema['orderType'] = new sfValidatorChoice(array(
      'choices' => array_keys($orderTypes)
    ));

    /*
     * Filters
     */
    foreach($this->dmAction->getParam('filters', array()) as $filter)
    {
      if (($filterModule = $this->dmModule->getAncestor($filter)) || ($filterModule = $this->dmModule->getAssociation($filter)))
      {
        $filterName = 'filter'.$filterModule->getModel();

        $this->widgetSchema[$filterName]    = new sfWidgetFormDoctrineSelect(array(
          'model'     => $filterModule->getModel(),
          'add_empty' => $this->allowFilterAutoRecordId($filterModule)
          ? sprintf('(%s) %s', dm::getI18n()->__('automatic'), $this->getFilterAutoRecord($filterModule)->__toString())
          : false
        ));

        $this->validatorSchema[$filterName] = new sfValidatorDoctrineChoice(array(
          'model'     => $filterModule->getModel(),
          'required'  => !$this->allowFilterAutoRecordId($filterModule)
        ));

        $this->widgetSchema[$filterName]->setLabel(dm::getI18n()->__($filterModule->getName()));
      }
    }

    $this->setDefaults($this->getDefaultsFromLastUpdated(array('maxPerPage', 'navTop', 'navBottom', 'view', 'orderField', 'orderType')));
  }

  protected function allowFilterAutoRecordId(dmModule $filterModule)
  {
    return dmContext::getInstance()->getPage()->dmModule->knows($filterModule);
  }

  protected function getFilterAutoRecord(dmModule $filterModule)
  {
    return dmContext::getInstance()->getPage()->record->getAncestorRecord($filterModule->getModel());
  }

  protected function getFirstDefaults()
  {
    $defaults = parent::getFirstDefaults();

    if ($firstOrderField = dmArray::first(array_keys($this->getAvailableOrderFields())))
    {
      $defaults['orderField'] = $firstOrderField;
    }

    return $defaults;
  }

  protected function renderContent($attributes)
  {
    return dmContext::getInstance()->getHelper()->renderPartial('dmWidget', 'forms/dmWidgetList', array('form' => $this));
  }

  protected function getAvailableOrderFields()
  {
    $fields = array();

    $allowedTypes = array('time', 'timestamp', 'date', 'enum', 'integer', 'string');

    foreach($this->dmModule->getTable()->getColumns() as $columnName => $column)
    {
      if (in_array($column['type'], $allowedTypes))
      {
        $fields[$columnName] = dm::getI18n()->__(dmString::humanize($columnName));
      }
    }

    return $fields;
  }

  protected function getOrderTypes()
  {
    return array(
      'asc'  => dm::getI18n()->__('Ascendant'),
      'desc' => dm::getI18n()->__('Descendant'),
      'rand' => dm::getI18n()->__('Random')
    );
  }

}