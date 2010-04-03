<?php

class dmWidgetSearchResultsForm extends dmWidgetPluginForm
{

  public function configure()
  {
    parent::configure();

    /// Max per page
    $this->widgetSchema['maxPerPage']     = new sfWidgetFormInputText(array(
      'label' => 'Per page'
    ), array(
      'size' => 3
    ));
    $this->validatorSchema['maxPerPage']  = new sfValidatorInteger(array(
      'min' => 0,
      'max' => 99999,
      'required' => false
    ));

    // Paginators top & bottom
    $this->widgetSchema['navTop']       = new sfWidgetFormInputCheckbox(array(
      'label' => 'Top'
    ));
    $this->validatorSchema['navTop']    = new sfValidatorBoolean();

    $this->widgetSchema['navBottom']    = new sfWidgetFormInputCheckbox(array(
      'label' => 'Bottom'
    ));
    $this->validatorSchema['navBottom'] = new sfValidatorBoolean();
  }

  protected function getFirstDefaults()
  {
    return array_merge(parent::getFirstDefaults(), array(
      'maxPerPage' => 10
    ));
  }

  protected function renderContent($attributes)
  {
    return $this->getHelper()->renderPartial('dmWidget', 'forms/dmWidgetSearchResults', array('form' => $this));
  }
}