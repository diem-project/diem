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


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmSentMail', 'column' => 'id')));
		}
		if($this->needsWidget('subject')){
			$this->setWidget('subject', new sfWidgetFormDmFilterInput());
			$this->setValidator('subject', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('body')){
			$this->setWidget('body', new sfWidgetFormDmFilterInput());
			$this->setValidator('body', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('from_email')){
			$this->setWidget('from_email', new sfWidgetFormDmFilterInput());
			$this->setValidator('from_email', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('to_email')){
			$this->setWidget('to_email', new sfWidgetFormDmFilterInput());
			$this->setValidator('to_email', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('cc_email')){
			$this->setWidget('cc_email', new sfWidgetFormDmFilterInput());
			$this->setValidator('cc_email', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('bcc_email')){
			$this->setWidget('bcc_email', new sfWidgetFormDmFilterInput());
			$this->setValidator('bcc_email', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('reply_to_email')){
			$this->setWidget('reply_to_email', new sfWidgetFormDmFilterInput());
			$this->setValidator('reply_to_email', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('sender_email')){
			$this->setWidget('sender_email', new sfWidgetFormDmFilterInput());
			$this->setValidator('sender_email', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('strategy')){
			$this->setWidget('strategy', new sfWidgetFormDmFilterInput());
			$this->setValidator('strategy', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('transport')){
			$this->setWidget('transport', new sfWidgetFormDmFilterInput());
			$this->setValidator('transport', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('culture')){
			$this->setWidget('culture', new sfWidgetFormDmFilterInput());
			$this->setValidator('culture', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('debug_string')){
			$this->setWidget('debug_string', new sfWidgetFormDmFilterInput());
			$this->setValidator('debug_string', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('created_at')){
			$this->setWidget('created_at', new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))));
			$this->setValidator('created_at', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['created_at']->getOption('choices')))));
		}



		if($this->needsWidget('template_list')){
			$this->setWidget('template_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmMailTemplate', 'expanded' => false)));
			$this->setValidator('template_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmMailTemplate', 'required' => true)));
		}

    

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
