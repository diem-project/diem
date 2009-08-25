<?php

class dmWidgetMenuForm extends dmWidgetStaticForm
{

	public function configure()
	{
    parent::configure();

    $this->widgetSchema['text'] = new sfWidgetFormInputText();

    $this->validatorSchema['text'] = new sfValidatorString(array('max_length' => 1024, 'required' => true));
	}

}