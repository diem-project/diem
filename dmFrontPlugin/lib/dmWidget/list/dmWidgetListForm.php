<?php

class dmWidgetListForm extends dmWidgetProjectModelForm
{
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
      'required' => false,
      'min' => 0,
      'max' => 99999
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
    foreach($this->dmAction->getOption('filters', array()) as $filter)
    {
      if (!$filterModule = $this->dmModule->getAncestor($filter))
      {
        if(!$filterModule = $this->dmModule->getAssociation($filter))
        {
          $filterModule = $this->dmModule->getLocal($filter);
        }  
      }
      
      if ($filterModule)
      {
        $filterName = $filterModule->getKey().'Filter';

        $this->widgetSchema[$filterName]    = new sfWidgetFormDoctrineChoice(array(
          'model'     => $filterModule->getModel(),
          'add_empty' => $this->allowFilterAutoRecordId($filterModule)
          ? sprintf('(%s) %s', $this->__('automatic'), $this->getFilterAutoRecord($filterModule)->__toString())
          : false
        ));

        $this->validatorSchema[$filterName] = new sfValidatorDoctrineChoice(array(
          'model'     => $filterModule->getModel(),
          'required'  => !$this->allowFilterAutoRecordId($filterModule)
        ));

        $this->widgetSchema[$filterName]->setLabel($this->__($filterModule->getName()));
      }
      else
      {
        throw new dmException(sprintf('Diem can not find a link between %s and %s modules', $this->dmModule, $filter));
      }
    }

    $this->setDefaults($this->getDefaultsFromLastUpdated(array('maxPerPage', 'navTop', 'navBottom', 'view', 'orderField', 'orderType')));
  
    if(!$this->getDefault('orderField') && $this->dmModule->getTable()->isSortable())
    {
      $this->setDefault('orderField', 'position');
    }
  }

  protected function allowFilterAutoRecordId(dmModule $filterModule)
  {
    return $this->getPage() ? $this->getPage()->getDmModule()->knows($filterModule) : false;
  }

  protected function getFilterAutoRecord(dmModule $filterModule)
  {
    return $this->getPage()->getRecord()->getAncestorRecord($filterModule->getModel());
  }

  protected function getFirstDefaults()
  {
    $defaults = array_merge(parent::getFirstDefaults(), array(
      'orderType'  => 'asc',
      'maxPerPage' => 5,
      'maxPerPage' => 0
    ));

    if ($firstOrderField = dmArray::first(array_keys($this->getAvailableOrderFields())))
    {
      $defaults['orderField'] = $firstOrderField;
    }

    return $defaults;
  }

  protected function renderContent($attributes)
  {
    return self::$serviceContainer->getService('helper')->renderPartial('dmWidget', 'forms/dmWidgetList', array('form' => $this));
  }

  protected function getAvailableOrderFields()
  {
    $fields = array();

    $allowedTypes = array('time', 'timestamp', 'date', 'enum', 'integer', 'string');

    foreach($this->dmModule->getTable()->getColumns() as $columnName => $column)
    {
      if (in_array($column['type'], $allowedTypes))
      {
        $fields[$columnName] = $this->__(dmString::humanize($columnName));
      }
    }

    return $fields;
  }

  protected function getOrderTypes()
  {
    return array(
      'asc'  => $this->__('Ascendant'),
      'desc' => $this->__('Descendant'),
      'rand' => $this->__('Random')
    );
  }

}