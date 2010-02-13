<?php

class dmWidgetSearchResultsForm extends dmWidgetPluginForm
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
      'min' => 0,
      'max' => 99999,
      'required' => false
    ));
  }

  protected function getFirstDefaults()
  {
    return array_merge(parent::getFirstDefaults(), array(
      'maxPerPage' =>10
    ));
  }
}