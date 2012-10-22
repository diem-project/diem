<?php

/**
 * DmTransUnit form base class.
 *
 * @method DmTransUnit getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmTransUnitForm extends BaseFormDoctrine
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
		if($this->needsWidget('source')){
			$this->setWidget('source', new sfWidgetFormTextarea());
			$this->setValidator('source', new sfValidatorString(array('max_length' => 60000)));
		}
		//column
		if($this->needsWidget('target')){
			$this->setWidget('target', new sfWidgetFormTextarea());
			$this->setValidator('target', new sfValidatorString(array('max_length' => 60000)));
		}
		//column
		if($this->needsWidget('meta')){
			$this->setWidget('meta', new sfWidgetFormInputText());
			$this->setValidator('meta', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('created_at')){
			$this->setWidget('created_at', new sfWidgetFormDateTime());
			$this->setValidator('created_at', new sfValidatorDateTime());
		}
		//column
		if($this->needsWidget('updated_at')){
			$this->setWidget('updated_at', new sfWidgetFormDateTime());
			$this->setValidator('updated_at', new sfValidatorDateTime());
		}



		//one to one
		if($this->needsWidget('dm_catalogue_id')){
			$this->setWidget('dm_catalogue_id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmCatalogue', 'expanded' => false)));
			$this->setValidator('dm_catalogue_id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmCatalogue', 'required' => true)));
		}




    $this->widgetSchema->setNameFormat('dm_trans_unit[%s]');

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
    return 'DmTransUnit';
  }

}
