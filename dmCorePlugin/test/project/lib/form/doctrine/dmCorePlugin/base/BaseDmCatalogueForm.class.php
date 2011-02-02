<?php

/**
 * DmCatalogue form base class.
 *
 * @method DmCatalogue getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmCatalogueForm extends BaseFormDoctrine
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
		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormInputText());
			$this->setValidator('name', new sfValidatorString(array('max_length' => 255)));
		}
		//column
		if($this->needsWidget('source_lang')){
			$this->setWidget('source_lang', new sfWidgetFormInputText());
			$this->setValidator('source_lang', new sfValidatorString(array('max_length' => 15)));
		}
		//column
		if($this->needsWidget('target_lang')){
			$this->setWidget('target_lang', new sfWidgetFormInputText());
			$this->setValidator('target_lang', new sfValidatorString(array('max_length' => 15)));
		}


		//one to many
		if($this->needsWidget('units_list')){
			$this->setWidget('units_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTransUnit', 'expanded' => true)));
			$this->setValidator('units_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTransUnit', 'required' => false)));
		}





    $this->widgetSchema->setNameFormat('dm_catalogue[%s]');

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
    return 'DmCatalogue';
  }

}
