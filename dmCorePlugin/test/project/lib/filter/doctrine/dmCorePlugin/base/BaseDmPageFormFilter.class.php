<?php

/**
 * DmPage filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmPageFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmPage', 'column' => 'id')));
		}
		if($this->needsWidget('module')){
			$this->setWidget('module', new sfWidgetFormDmFilterInput());
			$this->setValidator('module', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('action')){
			$this->setWidget('action', new sfWidgetFormDmFilterInput());
			$this->setValidator('action', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('record_id')){
			$this->setWidget('record_id', new sfWidgetFormDmFilterInput());
			$this->setValidator('record_id', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));
		}
		if($this->needsWidget('credentials')){
			$this->setWidget('credentials', new sfWidgetFormDmFilterInput());
			$this->setValidator('credentials', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('lft')){
			$this->setWidget('lft', new sfWidgetFormDmFilterInput());
			$this->setValidator('lft', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));
		}
		if($this->needsWidget('rgt')){
			$this->setWidget('rgt', new sfWidgetFormDmFilterInput());
			$this->setValidator('rgt', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));
		}
		if($this->needsWidget('level')){
			$this->setWidget('level', new sfWidgetFormDmFilterInput());
			$this->setValidator('level', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));
		}




    
    $this->mergeI18nForm();


    $this->widgetSchema->setNameFormat('dm_page_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmPage';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'module'       => 'Text',
      'action'       => 'Text',
      'record_id'    => 'Number',
      'credentials'  => 'Text',
      'lft'          => 'Number',
      'rgt'          => 'Number',
      'level'        => 'Number',
      'id'           => 'Number',
      'slug'         => 'Text',
      'name'         => 'Text',
      'title'        => 'Text',
      'h1'           => 'Text',
      'description'  => 'Text',
      'keywords'     => 'Text',
      'auto_mod'     => 'Text',
      'is_active'    => 'Boolean',
      'is_secure'    => 'Boolean',
      'is_indexable' => 'Boolean',
      'lang'         => 'Text',
    );
  }
}
