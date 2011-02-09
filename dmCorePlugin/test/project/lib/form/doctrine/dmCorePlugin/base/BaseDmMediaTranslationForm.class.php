<?php

/**
 * DmMediaTranslation form base class.
 *
 * @method DmMediaTranslation getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmMediaTranslationForm extends BaseFormDoctrine
{
  public function setup()
  {
    parent::setup();

		//column
		if($this->needsWidget('legend')){
			$this->setWidget('legend', new sfWidgetFormInputText());
			$this->setValidator('legend', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('author')){
			$this->setWidget('author', new sfWidgetFormInputText());
			$this->setValidator('author', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('license')){
			$this->setWidget('license', new sfWidgetFormInputText());
			$this->setValidator('license', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('lang')){
			$this->setWidget('lang', new sfWidgetFormInputHidden());
			$this->setValidator('lang', new sfValidatorChoice(array('choices' => array($this->getObject()->get('lang')), 'empty_value' => $this->getObject()->get('lang'), 'required' => false)));
		}



		//one to one
		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmMedia', 'expanded' => false)));
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmMedia', 'required' => false)));
		}




    $this->widgetSchema->setNameFormat('dm_media_translation[%s]');

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
    return 'DmMediaTranslation';
  }

}
