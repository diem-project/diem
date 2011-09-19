<?php

/**
 * DmError filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmErrorFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmError', 'column' => 'id')));
		}
		if($this->needsWidget('php_class')){
			$this->setWidget('php_class', new sfWidgetFormDmFilterInput());
			$this->setValidator('php_class', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormDmFilterInput());
			$this->setValidator('name', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('description')){
			$this->setWidget('description', new sfWidgetFormDmFilterInput());
			$this->setValidator('description', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('module')){
			$this->setWidget('module', new sfWidgetFormDmFilterInput());
			$this->setValidator('module', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('action')){
			$this->setWidget('action', new sfWidgetFormDmFilterInput());
			$this->setValidator('action', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('uri')){
			$this->setWidget('uri', new sfWidgetFormDmFilterInput());
			$this->setValidator('uri', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('env')){
			$this->setWidget('env', new sfWidgetFormDmFilterInput());
			$this->setValidator('env', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
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




    

    $this->widgetSchema->setNameFormat('dm_error_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmError';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'php_class'   => 'Text',
      'name'        => 'Text',
      'description' => 'Text',
      'module'      => 'Text',
      'action'      => 'Text',
      'uri'         => 'Text',
      'env'         => 'Text',
      'created_at'  => 'Date',
    );
  }
}
