<?php

/**
 * DmTestCommentVersion form base class.
 *
 * @method DmTestCommentVersion getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmTestCommentVersionForm extends BaseFormDoctrine
{
  public function setup()
  {
    parent::setup();

		//column
		if($this->needsWidget('post_id')){
			$this->setWidget('post_id', new sfWidgetFormInputText());
			$this->setValidator('post_id', new sfValidatorInteger());
		}
		//column
		if($this->needsWidget('author')){
			$this->setWidget('author', new sfWidgetFormInputText());
			$this->setValidator('author', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('body')){
			$this->setWidget('body', new sfWidgetFormTextarea());
			$this->setValidator('body', new sfValidatorString(array('required' => false)));
		}
		//column
		if($this->needsWidget('is_active')){
			$this->setWidget('is_active', new sfWidgetFormInputCheckbox());
			$this->setValidator('is_active', new sfValidatorBoolean(array('required' => false)));
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
		//column
		if($this->needsWidget('version')){
			$this->setWidget('version', new sfWidgetFormInputHidden());
			$this->setValidator('version', new sfValidatorChoice(array('choices' => array($this->getObject()->get('version')), 'empty_value' => $this->getObject()->get('version'), 'required' => false)));
		}



		//one to one
		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmTestComment', 'expanded' => false)));
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmTestComment', 'required' => false)));
		}




    $this->widgetSchema->setNameFormat('dm_test_comment_version[%s]');

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
    return 'DmTestCommentVersion';
  }

}
