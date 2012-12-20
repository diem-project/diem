<?php

/**
 * DmTransUnit filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTransUnitFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmTransUnit', 'column' => 'id')));
		}
		if($this->needsWidget('source')){
			$this->setWidget('source', new sfWidgetFormDmFilterInput());
			$this->setValidator('source', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('target')){
			$this->setWidget('target', new sfWidgetFormDmFilterInput());
			$this->setValidator('target', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('meta')){
			$this->setWidget('meta', new sfWidgetFormDmFilterInput());
			$this->setValidator('meta', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
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



		if($this->needsWidget('dm_catalogue_list')){
			$this->setWidget('dm_catalogue_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmCatalogue', 'expanded' => false)));
			$this->setValidator('dm_catalogue_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmCatalogue', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_trans_unit_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmTransUnit';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'dm_catalogue_id' => 'ForeignKey',
      'source'          => 'Text',
      'target'          => 'Text',
      'meta'            => 'Text',
      'created_at'      => 'Date',
      'updated_at'      => 'Date',
    );
  }
}
