<?php

class dmWidgetAdvancedSearchResultsForm extends dmWidgetPluginForm
{
	protected
	$firstDefaults = array(
    'maxPerPage' =>10
	);

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
      'max' => 9999
    ));
	}

}