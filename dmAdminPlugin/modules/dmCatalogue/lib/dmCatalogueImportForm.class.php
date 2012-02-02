<?php

class dmCatalogueImportForm extends dmForm
{
  public function configure()
  {
    parent::configure();

    $this->setWidgets(array(
      'file' => new sfWidgetFormDmInputFile(),
      //'override' => new sfWidgetFormInputCheckbox(),
    ));
    
    //$this->widgetSchema['override']->setLabel('Override existing sentences?');
 
    $this->setValidators(array(
      'file' => new sfValidatorFile(array(
        //'mime_types' => array('plain/text')
      )),
      //'override' => new sfValidatorBoolean()
    ));
  }
}