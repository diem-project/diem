<?php

/**
 * DmSentMail form base class.
 *
 * @method DmSentMail getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmSentMailForm extends BaseFormDoctrine
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
		if($this->needsWidget('strategy')){
			$this->setWidget('strategy', new sfWidgetFormInputText());
			$this->setValidator('strategy', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('transport')){
			$this->setWidget('transport', new sfWidgetFormInputText());
			$this->setValidator('transport', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('culture')){
			$this->setWidget('culture', new sfWidgetFormInputText());
			$this->setValidator('culture', new sfValidatorString(array('max_length' => 16, 'required' => false)));
		}
		//column
		if($this->needsWidget('debug_string')){
			$this->setWidget('debug_string', new sfWidgetFormTextarea());
			$this->setValidator('debug_string', new sfValidatorString(array('required' => false)));
		}
		//column
		if($this->needsWidget('created_at')){
			$this->setWidget('created_at', new sfWidgetFormDateTime());
			$this->setValidator('created_at', new sfValidatorDateTime());
		}



		//one to one
		if($this->needsWidget('dm_mail_template_id')){
			$this->setWidget('dm_mail_template_id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmMailTemplate', 'expanded' => false)));
			$this->setValidator('dm_mail_template_id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmMailTemplate', 'required' => false)));
		}




    $this->widgetSchema->setNameFormat('dm_sent_mail[%s]');

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
    return 'DmSentMail';
  }

}
