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
 */
abstract class BaseDmSentMailForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'dm_mail_template_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Template'), 'add_empty' => true)),
      'subject'             => new sfWidgetFormTextarea(),
      'body'                => new sfWidgetFormTextarea(),
      'from_email'          => new sfWidgetFormTextarea(),
      'to_email'            => new sfWidgetFormTextarea(),
      'cc_email'            => new sfWidgetFormTextarea(),
      'bcc_email'           => new sfWidgetFormTextarea(),
      'reply_to_email'      => new sfWidgetFormTextarea(),
      'sender_email'        => new sfWidgetFormTextarea(),
      'strategy'            => new sfWidgetFormInputText(),
      'transport'           => new sfWidgetFormInputText(),
      'culture'             => new sfWidgetFormInputText(),
      'debug_string'        => new sfWidgetFormTextarea(),
      'created_at'          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'dm_mail_template_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Template'), 'required' => false)),
      'subject'             => new sfValidatorString(array('max_length' => 5000)),
      'body'                => new sfValidatorString(),
      'from_email'          => new sfValidatorString(array('max_length' => 5000)),
      'to_email'            => new sfValidatorString(array('max_length' => 5000, 'required' => false)),
      'cc_email'            => new sfValidatorString(array('max_length' => 5000, 'required' => false)),
      'bcc_email'           => new sfValidatorString(array('max_length' => 5000, 'required' => false)),
      'reply_to_email'      => new sfValidatorString(array('max_length' => 5000, 'required' => false)),
      'sender_email'        => new sfValidatorString(array('max_length' => 5000, 'required' => false)),
      'strategy'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'transport'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'culture'             => new sfValidatorString(array('max_length' => 16, 'required' => false)),
      'debug_string'        => new sfValidatorString(array('required' => false)),
      'created_at'          => new sfValidatorDateTime(),
    ));

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