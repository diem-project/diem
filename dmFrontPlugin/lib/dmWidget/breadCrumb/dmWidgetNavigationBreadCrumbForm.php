<?php

class dmWidgetNavigationBreadCrumbForm extends dmWidgetPluginForm
{

  public function configure()
  {
    $this->widgetSchema['separator'] = new sfWidgetFormInputText();
    $this->widgetSchema['includeCurrent'] = new sfWidgetFormInputCheckBox();

    $this->validatorSchema['separator'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $this->validatorSchema['includeCurrent']  = new sfValidatorBoolean();
    
    $this->widgetSchema['includeCurrent']->setLabel('Include current page');

    $this->setDefaults($this->getDefaultsFromLastUpdated(array('separator', 'includeCurrent')));

    parent::configure();
  }

  protected function getFirstDefaults()
  {
    return array_merge(parent::getFirstDefaults(), array(
      'separator'      => '>',
      'includeCurrent' => true
    ));
  }
}