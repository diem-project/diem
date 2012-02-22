<?php

class dmCatalogueImportForm extends dmForm
{
  public function configure()
  {
    parent::configure();

    $this->widgetSchema['file'] = new sfWidgetFormDmInputFile();
    $this->widgetSchema['override'] = new sfWidgetFormInputCheckbox();
    
    $this->widgetSchema['override']->setLabel('Override?');
 
    $this->validatorSchema['file'] = new sfValidatorFile(array(
      //'mime_types' => array('plain/text')
    ));
    $this->validatorSchema['override'] = new sfValidatorBoolean();
  }
}