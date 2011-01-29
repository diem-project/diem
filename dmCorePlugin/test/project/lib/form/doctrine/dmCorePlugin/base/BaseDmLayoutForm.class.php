<?php

/**
 * DmLayout form base class.
 *
 * @method DmLayout getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmLayoutForm extends BaseFormDoctrine
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
		if($this->needsWidget('template')){
			$this->setWidget('template', new sfWidgetFormInputText());
			$this->setValidator('template', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('css_class')){
			$this->setWidget('css_class', new sfWidgetFormInputText());
			$this->setValidator('css_class', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}


		//one to many
		if($this->needsWidget('page_views_list')){
			$this->setWidget('page_views_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmPageView', 'expanded' => true)));
			$this->setValidator('page_views_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmPageView', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('areas_list')){
			$this->setWidget('areas_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmArea', 'expanded' => true)));
			$this->setValidator('areas_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmArea', 'required' => false)));
		}





    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmLayout', 'column' => array('name')))
    );

    $this->widgetSchema->setNameFormat('dm_layout[%s]');

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
    return 'DmLayout';
  }

}
