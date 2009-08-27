<?php

class dmWidgetContentTextForm extends dmWidgetContentMediaForm
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
    
    $this->widgetSchema['mediaLink'] = new sfWidgetFormInputText();
    $this->validatorSchema['mediaLink'] = new sfValidatorString(array('required' => false));
	}
	
  protected function renderContent($attributes)
  {
    return dmContext::getInstance()->getHelper()->renderPartial('dmWidget', 'forms/dmWidgetContentText', array(
      'form' => $this,
      'baseTabId' => 'dm_widget_text_'.$this->dmWidget->id,
      'hasMedia' => (boolean) $this->getValueOrDefault('mediaId')
    ));
  }
  
  /*
   * Disable media source validation
   * because a text widget may have no media
   */
  public function checkMediaSource($validator, $values)
  {
    return $values;
  }
}