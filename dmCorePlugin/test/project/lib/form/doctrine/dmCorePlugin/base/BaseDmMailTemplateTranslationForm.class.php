<?php

/**
 * DmMailTemplateTranslation form base class.
 *
 * @method DmMailTemplateTranslation getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmMailTemplateTranslationForm extends BaseFormDoctrine
{
  public function setup()
  {
    parent::setup();

		//column
		if($this->needsWidget('description')){
			$this->setWidget('description', new sfWidgetFormTextarea());
			$this->setValidator('description', new sfValidatorString(array('max_length' => 5000, 'required' => false)));
		}
		//column
		if($this->needsWidget('subject')){
			$this->setWidget('subject', new sfWidgetFormTextarea());
			$this->setValidator('subject', new sfValidatorString(array('max_length' => 5000)));
		}
		//column
		if($this->needsWidget('body')){
			$this->setWidget('body', new sfWidgetFormTextarea());
			$this->setValidator('body', new sfValidatorString());
		}
		//column
		if($this->needsWidget('from_email')){
			$this->setWidget('from_email', new sfWidgetFormTextarea());
			$this->setValidator('from_email', new sfValidatorString(array('max_length' => 5000)));
		}
		//column
		if($this->needsWidget('to_email')){
			$this->setWidget('to_email', new sfWidgetFormTextarea());
			$this->setValidator('to_email', new sfValidatorString(array('max_length' => 5000, 'required' => false)));
		}
		//column
		if($this->needsWidget('cc_email')){
			$this->setWidget('cc_email', new sfWidgetFormTextarea());
			$this->setValidator('cc_email', new sfValidatorString(array('max_length' => 5000, 'required' => false)));
		}
		//column
		if($this->needsWidget('bcc_email')){
			$this->setWidget('bcc_email', new sfWidgetFormTextarea());
			$this->setValidator('bcc_email', new sfValidatorString(array('max_length' => 5000, 'required' => false)));
		}
		//column
		if($this->needsWidget('reply_to_email')){
			$this->setWidget('reply_to_email', new sfWidgetFormTextarea());
			$this->setValidator('reply_to_email', new sfValidatorString(array('max_length' => 5000, 'required' => false)));
		}
		//column
		if($this->needsWidget('sender_email')){
			$this->setWidget('sender_email', new sfWidgetFormTextarea());
			$this->setValidator('sender_email', new sfValidatorString(array('max_length' => 5000, 'required' => false)));
		}
		//column
		if($this->needsWidget('list_unsuscribe')){
			$this->setWidget('list_unsuscribe', new sfWidgetFormTextarea());
			$this->setValidator('list_unsuscribe', new sfValidatorString(array('max_length' => 5000, 'required' => false)));
		}
		//column
		if($this->needsWidget('is_html')){
			$this->setWidget('is_html', new sfWidgetFormInputCheckbox());
			$this->setValidator('is_html', new sfValidatorBoolean(array('required' => false)));
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
			$this->setWidget('id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmMailTemplate', 'expanded' => false)));
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmMailTemplate', 'required' => false)));
		}




    $this->widgetSchema->setNameFormat('dm_mail_template_translation[%s]');

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
    return 'DmMailTemplateTranslation';
  }

}
