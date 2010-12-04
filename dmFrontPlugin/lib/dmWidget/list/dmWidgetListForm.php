<?php

class dmWidgetListForm extends dmWidgetProjectModelForm
{
  public function configure()
  {
    // Max per page
    $this->widgetSchema['maxPerPage']     = new sfWidgetFormInputText(array(), array(
      'size' => 3
    ));
    $this->validatorSchema['maxPerPage']  = new sfValidatorInteger(array(
      'required' => false,
      'min' => 0,
      'max' => 99999
    ));

    // Paginators top & bottom
    $this->widgetSchema['navTop']       = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['navTop']    = new sfValidatorBoolean();

    $this->widgetSchema['navBottom']    = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['navBottom'] = new sfValidatorBoolean();

    // Order field selection
    $orderFields = $this->getAvailableOrderFields();
    $this->widgetSchema['orderField']    = new sfWidgetFormSelect(array(
      'choices' => $orderFields
    ));
    $this->validatorSchema['orderField'] = new sfValidatorChoice(array(
      'choices' => array_keys($orderFields)
    ));

    // Order type selection
    $orderTypes = $this->getOrderTypes();
    $this->widgetSchema['orderType']    = new sfWidgetFormSelect(array(
      'choices' => $orderTypes
    ));
    $this->validatorSchema['orderType'] = new sfValidatorChoice(array(
      'choices' => array_keys($orderTypes)
    ));

    // Filters
    foreach($this->dmComponent->getOption('filters', array()) as $filter)
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
          'label'     => $this->__($filterModule->getName()),
          'model'     => $filterModule->getModel(),
          'add_empty' => $this->allowFilterAutoRecordId($filterModule)
          ? sprintf('(%s) %s', $this->__('contextual'), $this->getFilterAutoRecord($filterModule)->__toString())
          : false
        ));

        $this->validatorSchema[$filterName] = new sfValidatorDoctrineChoice(array(
          'model'     => $filterModule->getModel(),
          'required'  => !$this->allowFilterAutoRecordId($filterModule)
        ));
      }
      elseif($column = $this->dmModule->getTable()->getColumn($filter))
      {
        $filterName = $filter.'Filter';
        
        if('boolean' !== $column['type'])
        {
          throw new dmException(sprintf('Diem can only filter by module or boolean fields, %s given', $column['type']));
        }

        $this->widgetSchema[$filterName]    = new sfWidgetFormInputCheckbox(array(
          'label'     => $this->__(dmString::humanize($filter))
        ));

        $this->validatorSchema[$filterName] = new sfValidatorBoolean();
      }
      else
      {
        throw new dmException(sprintf('Diem can not filter %s by %s', $this->dmModule, $filter));
      }
    }

    $this->setDefaults($this->getDefaultsFromLastUpdated(array('maxPerPage', 'navTop', 'navBottom', 'view', 'orderField', 'orderType')));
  
    parent::configure();
  }

  protected function allowFilterAutoRecordId(dmModule $filterModule)
  {
    if($page = $this->getPage())
    {
      return $page->hasRecord() && $page->getDmModule()->knows($filterModule);
    }

    return false;
  }

  protected function getFilterAutoRecord(dmModule $filterModule)
  {
    return $this->getPage()->getRecord()->getAncestorRecord($filterModule->getModel());
  }

  protected function getFirstDefaults()
  {
    $defaults = array_merge(parent::getFirstDefaults(), array(
      'orderType'  => 'asc',
      'maxPerPage' => 5
    ));
    
    if(!$this->getDefault('orderField') && $this->dmModule->getTable()->isSortable())
    {
      $defaults['orderField'] = 'position';
    }

    return $defaults;
  }

  protected function renderContent($attributes)
  {
    return $this->getHelper()->renderPartial('dmWidget', 'forms/dmWidgetList', array('form' => $this));
  }

  protected function getAvailableOrderFields()
  {
    $fields = array();

    $allowedTypes = array('time', 'timestamp', 'date', 'enum', 'integer', 'string', 'float');
    $skipColumns  = $this->dmModule->getTable()->hasI18n() ? array('lang') : array();

    foreach($this->dmModule->getTable()->getAllColumns() as $columnName => $column)
    {
      if (in_array($column['type'], $allowedTypes) && !in_array($columnName, $skipColumns))
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
