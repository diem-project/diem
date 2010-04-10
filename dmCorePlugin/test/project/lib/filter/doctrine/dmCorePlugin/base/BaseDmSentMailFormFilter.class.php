<?php

/**
 * DmSentMail filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmSentMailFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'dm_mail_template_id' => new sfWidgetFormDoctrineChoice(array('model' => 'DmMailTemplate', 'add_empty' => true)),
      'subject'             => new sfWidgetFormDmFilterInput(),
      'body'                => new sfWidgetFormDmFilterInput(),
      'from_email'          => new sfWidgetFormDmFilterInput(),
      'to_email'            => new sfWidgetFormDmFilterInput(),
      'cc_email'            => new sfWidgetFormDmFilterInput(),
      'bcc_email'           => new sfWidgetFormDmFilterInput(),
      'reply_to_email'      => new sfWidgetFormDmFilterInput(),
      'sender_email'        => new sfWidgetFormDmFilterInput(),
      'strategy'            => new sfWidgetFormDmFilterInput(),
      'transport'           => new sfWidgetFormDmFilterInput(),
      'culture'             => new sfWidgetFormDmFilterInput(),
      'debug_string'        => new sfWidgetFormDmFilterInput(),
      'created_at'          => new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))),
    ));

    $this->setValidators(array(
      'dm_mail_template_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Template'), 'column' => 'id')),
      'subject'             => new sfValidatorPass(array('required' => false)),
      'body'                => new sfValidatorPass(array('required' => false)),
      'from_email'          => new sfValidatorPass(array('required' => false)),
      'to_email'            => new sfValidatorPass(array('required' => false)),
      'cc_email'            => new sfValidatorPass(array('required' => false)),
      'bcc_email'           => new sfValidatorPass(array('required' => false)),
      'reply_to_email'      => new sfValidatorPass(array('required' => false)),
      'sender_email'        => new sfValidatorPass(array('required' => false)),
      'strategy'            => new sfValidatorPass(array('required' => false)),
      'transport'           => new sfValidatorPass(array('required' => false)),
      'culture'             => new sfValidatorPass(array('required' => false)),
      'debug_string'        => new sfValidatorPass(array('required' => false)),
      'created_at'          => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['created_at']->getOption('choices')))),
    ));

    $this->widgetSchema->setNameFormat('dm_sent_mail_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmSentMail';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'dm_mail_template_id' => 'ForeignKey',
      'subject'             => 'Text',
      'body'                => 'Text',
      'from_email'          => 'Text',
      'to_email'            => 'Text',
      'cc_email'            => 'Text',
      'bcc_email'           => 'Text',
      'reply_to_email'      => 'Text',
      'sender_email'        => 'Text',
      'strategy'            => 'Text',
      'transport'           => 'Text',
      'culture'             => 'Text',
      'debug_string'        => 'Text',
      'created_at'          => 'Date',
    );
  }
}
