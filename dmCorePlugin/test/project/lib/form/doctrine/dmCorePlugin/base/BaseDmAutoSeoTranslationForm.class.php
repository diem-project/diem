<?php

/**
 * DmAutoSeoTranslation form base class.
 *
 * @method DmAutoSeoTranslation getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmAutoSeoTranslationForm extends BaseFormDoctrine
{
  public function setup()
  {
    parent::setup();

		//column
		if($this->needsWidget('slug')){
			$this->setWidget('slug', new sfWidgetFormInputText());
			$this->setValidator('slug', new sfValidatorString(array('max_length' => 255)));
		}
		//column
		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormInputText());
			$this->setValidator('name', new sfValidatorString(array('max_length' => 255)));
		}
		//column
		if($this->needsWidget('title')){
			$this->setWidget('title', new sfWidgetFormInputText());
			$this->setValidator('title', new sfValidatorString(array('max_length' => 255)));
		}
		//column
		if($this->needsWidget('h1')){
			$this->setWidget('h1', new sfWidgetFormInputText());
			$this->setValidator('h1', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('description')){
			$this->setWidget('description', new sfWidgetFormInputText());
			$this->setValidator('description', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('keywords')){
			$this->setWidget('keywords', new sfWidgetFormInputText());
			$this->setValidator('keywords', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('strip_words')){
			$this->setWidget('strip_words', new sfWidgetFormTextarea());
			$this->setValidator('strip_words', new sfValidatorString(array('max_length' => 10000, 'required' => false)));
		}
		//column
		if($this->needsWidget('lang')){
			$this->setWidget('lang', new sfWidgetFormInputHidden());
			$this->setValidator('lang', new sfValidatorChoice(array('choices' => array($this->getObject()->get('lang')), 'empty_value' => $this->getObject()->get('lang'), 'required' => false)));
		}



		//one to one
		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmAutoSeo', 'expanded' => false)));
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmAutoSeo', 'required' => false)));
		}




    $this->widgetSchema->setNameFormat('dm_auto_seo_translation[%s]');

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
    return 'DmAutoSeoTranslation';
  }

}
