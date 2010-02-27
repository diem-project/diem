<?php

class dmSearchForm extends BaseForm
{

  public function configure()
  {
    $this->setName('search')
    ->widgetSchema->setNameFormat('%s');
    
    $this->widgetSchema['query'] = new sfWidgetFormInputText();
    
    $this->validatorSchema['query'] = new sfValidatorString(array('required' => false));
    
    $this->removeCsrfProtection();
  }
  
}