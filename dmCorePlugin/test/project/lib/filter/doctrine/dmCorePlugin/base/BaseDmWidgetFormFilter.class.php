<?php

/**
 * DmWidget filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmWidgetFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmWidget', 'column' => 'id')));
		}
		if($this->needsWidget('module')){
			$this->setWidget('module', new sfWidgetFormDmFilterInput());
			$this->setValidator('module', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('action')){
			$this->setWidget('action', new sfWidgetFormDmFilterInput());
			$this->setValidator('action', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('css_class')){
			$this->setWidget('css_class', new sfWidgetFormDmFilterInput());
			$this->setValidator('css_class', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('position')){
			$this->setWidget('position', new sfWidgetFormDmFilterInput());
			$this->setValidator('position', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));
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



		if($this->needsWidget('zone_list')){
			$this->setWidget('zone_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmZone', 'expanded' => false)));
			$this->setValidator('zone_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmZone', 'required' => true)));
		}

    
    $this->mergeI18nForm();


    $this->widgetSchema->setNameFormat('dm_widget_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmWidget';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'dm_zone_id' => 'ForeignKey',
      'module'     => 'Text',
      'action'     => 'Text',
      'css_class'  => 'Text',
      'position'   => 'Number',
      'updated_at' => 'Date',
      'id'         => 'Number',
      'value'      => 'Text',
      'lang'       => 'Text',
    );
  }
}
