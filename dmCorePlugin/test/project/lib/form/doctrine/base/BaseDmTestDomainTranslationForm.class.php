<?php

/**
 * DmTestDomainTranslation form base class.
 *
 * @method DmTestDomainTranslation getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmTestDomainTranslationForm extends BaseFormDoctrine
{
  public function setup()
  {
    parent::setup();

		//column
		if($this->needsWidget('title')){
			$this->setWidget('title', new sfWidgetFormInputText());
			$this->setValidator('title', new sfValidatorString(array('max_length' => 255)));
		}
		//column
		if($this->needsWidget('is_active')){
			$this->setWidget('is_active', new sfWidgetFormInputCheckbox());
			$this->setValidator('is_active', new sfValidatorBoolean(array('required' => false)));
		}
		//column
		if($this->needsWidget('lang')){
			$this->setWidget('lang', new sfWidgetFormInputHidden());
			$this->setValidator('lang', new sfValidatorChoice(array('choices' => array($this->getObject()->get('lang')), 'empty_value' => $this->getObject()->get('lang'), 'required' => false)));
		}



		//one to one
		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmTestDomain', 'expanded' => false)));
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmTestDomain', 'required' => false)));
		}
		//one to one
		if($this->needsWidget('created_by')){
			$this->setWidget('created_by', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'expanded' => false)));
			$this->setValidator('created_by', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'required' => false)));
		}
		//one to one
		if($this->needsWidget('updated_by')){
			$this->setWidget('updated_by', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'expanded' => false)));
			$this->setValidator('updated_by', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'required' => false)));
		}




    $this->widgetSchema->setNameFormat('dm_test_domain_translation[%s]');

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
    return 'DmTestDomainTranslation';
  }

}
