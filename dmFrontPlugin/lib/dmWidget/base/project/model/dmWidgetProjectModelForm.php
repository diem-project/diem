<?php

abstract class dmWidgetProjectModelForm extends dmWidgetProjectForm
{

	public function configure()
	{
		parent::configure();

    $this->widgetSchema['view']     = new sfWidgetFormSelectRadio(array(
      'choices' => $this->getAvailableViews()
    ));

    $this->validatorSchema['view']  = new sfValidatorChoice(array(
      'choices' => array_keys($this->getAvailableViews())
    ));
	}

	protected function getAvailableViews()
	{
		return $this->dmModule->getViews();
	}

  protected function getFirstDefaults()
  {
    $defaults = parent::getFirstDefaults();

    if ($firstView = dmArray::first(array_keys($this->getAvailableViews())))
    {
    	$defaults['view'] = $firstView;
    }

    return $defaults;
  }

}