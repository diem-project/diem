<?php

/**
 * DmError form base class.
 *
 * @method DmError getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmErrorForm extends BaseFormDoctrine
{
  public function setup()
  {
    parent::setup();

		//column
		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormInputHidden());
			$this->setValidator('id', new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)));
		}
		//column
		if($this->needsWidget('php_class')){
			$this->setWidget('php_class', new sfWidgetFormInputText());
			$this->setValidator('php_class', new sfValidatorString(array('max_length' => 127)));
		}
		//column
		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormInputText());
			$this->setValidator('name', new sfValidatorString(array('max_length' => 255)));
		}
		//column
		if($this->needsWidget('description')){
			$this->setWidget('description', new sfWidgetFormTextarea());
			$this->setValidator('description', new sfValidatorString(array('max_length' => 60000, 'required' => false)));
		}
		//column
		if($this->needsWidget('module')){
			$this->setWidget('module', new sfWidgetFormInputText());
			$this->setValidator('module', new sfValidatorString(array('max_length' => 127, 'required' => false)));
		}
		//column
		if($this->needsWidget('action')){
			$this->setWidget('action', new sfWidgetFormInputText());
			$this->setValidator('action', new sfValidatorString(array('max_length' => 127, 'required' => false)));
		}
		//column
		if($this->needsWidget('uri')){
			$this->setWidget('uri', new sfWidgetFormInputText());
			$this->setValidator('uri', new sfValidatorString(array('max_length' => 255)));
		}
		//column
		if($this->needsWidget('env')){
			$this->setWidget('env', new sfWidgetFormInputText());
			$this->setValidator('env', new sfValidatorString(array('max_length' => 63)));
		}
		//column
		if($this->needsWidget('created_at')){
			$this->setWidget('created_at', new sfWidgetFormDateTime());
			$this->setValidator('created_at', new sfValidatorDateTime());
		}







    $this->widgetSchema->setNameFormat('dm_error[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
    
    // Unset automatic fields like 'created_at', 'updated_at', 'position'
    // override this method in your form to keep them
    parent::unsetAutoFields();
  }


  protected function doBind(array $values)
  {
    parent::doBind($values);
  }
  
  public function processValues($values)
  {
    $values = parent::processValues($values);
    return $values;
  }
  
  protected function doUpdateObject($values)
  {
    parent::doUpdateObject($values);
  }

  public function getModelName()
  {
    return 'DmError';
  }

}
