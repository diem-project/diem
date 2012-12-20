<?php

/**
 * DmTestCommentVersion filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTestCommentVersionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('post_id')){
			$this->setWidget('post_id', new sfWidgetFormDmFilterInput());
			$this->setValidator('post_id', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));
		}
		if($this->needsWidget('author')){
			$this->setWidget('author', new sfWidgetFormDmFilterInput());
			$this->setValidator('author', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('body')){
			$this->setWidget('body', new sfWidgetFormDmFilterInput());
			$this->setValidator('body', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('is_active')){
			$this->setWidget('is_active', new sfWidgetFormChoice(array('choices' => array('' => $this->getI18n()->__('yes or no', array(), 'dm'), 1 => $this->getI18n()->__('yes', array(), 'dm'), 0 => $this->getI18n()->__('no', array(), 'dm')))));
			$this->setValidator('is_active', new sfValidatorBoolean());
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
		if($this->needsWidget('updated_at')){
			$this->setWidget('updated_at', new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))));
			$this->setValidator('updated_at', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['updated_at']->getOption('choices')))));
		}
		if($this->needsWidget('version')){
			$this->setWidget('version', new sfWidgetFormDmFilterInput());
			$this->setValidator('version', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmTestCommentVersion', 'column' => 'version')));
		}



		if($this->needsWidget('dm_test_comment_list')){
			$this->setWidget('dm_test_comment_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmTestComment', 'expanded' => false)));
			$this->setValidator('dm_test_comment_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmTestComment', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_test_comment_version_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmTestCommentVersion';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'post_id'    => 'Number',
      'author'     => 'Text',
      'body'       => 'Text',
      'is_active'  => 'Boolean',
      'created_at' => 'Date',
      'updated_at' => 'Date',
      'version'    => 'Number',
    );
  }
}
