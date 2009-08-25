<?php

class dmWidgetContentTextForm extends dmWidgetPluginForm
{

	public function configure()
	{
    parent::configure();
    
    $this->widgetSchema['title'] = new sfWidgetFormInputText();
    $this->validatorSchema['title'] = new sfValidatorString(array('required' => false));
    
    $this->widgetSchema['titleLink'] = new sfWidgetFormInputText();
    $this->validatorSchema['titleLink'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['text'] = new sfWidgetFormTextarea();
    $this->validatorSchema['text'] = new sfValidatorString(array('required' => false));
    
    $mediaForm = new dmWidgetContentMediaForm($this->dmWidget);
    $this->mergeForm($mediaForm);
	}

}