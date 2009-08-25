<?php

class DmWidgetFrontForm extends DmWidgetForm
{

	public function setup()
  {
    $this->setWidgets(array(
      'css_class'  => new sfWidgetFormInputText()
    ));

    $this->setValidators(array(
      'css_class'  => new sfValidatorString(array('max_length' => 127, 'required' => false))
    ));

    $this->widgetSchema->setNameFormat('dm_widget[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

}