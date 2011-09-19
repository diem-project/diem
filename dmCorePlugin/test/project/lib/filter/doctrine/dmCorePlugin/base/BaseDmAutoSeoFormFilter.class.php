<?php

/**
 * DmAutoSeo filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmAutoSeoFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmAutoSeo', 'column' => 'id')));
		}
		if($this->needsWidget('module')){
			$this->setWidget('module', new sfWidgetFormDmFilterInput());
			$this->setValidator('module', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('action')){
			$this->setWidget('action', new sfWidgetFormDmFilterInput());
			$this->setValidator('action', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
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




    
    $this->mergeI18nForm();


    $this->widgetSchema->setNameFormat('dm_auto_seo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmAutoSeo';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'module'      => 'Text',
      'action'      => 'Text',
      'created_at'  => 'Date',
      'updated_at'  => 'Date',
      'id'          => 'Number',
      'slug'        => 'Text',
      'name'        => 'Text',
      'title'       => 'Text',
      'h1'          => 'Text',
      'description' => 'Text',
      'keywords'    => 'Text',
      'strip_words' => 'Text',
      'lang'        => 'Text',
    );
  }
}
