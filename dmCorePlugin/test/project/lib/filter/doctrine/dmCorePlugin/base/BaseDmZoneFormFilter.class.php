<?php

/**
 * DmZone filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmZoneFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmZone', 'column' => 'id')));
		}
		if($this->needsWidget('css_class')){
			$this->setWidget('css_class', new sfWidgetFormDmFilterInput());
			$this->setValidator('css_class', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('width')){
			$this->setWidget('width', new sfWidgetFormDmFilterInput());
			$this->setValidator('width', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('position')){
			$this->setWidget('position', new sfWidgetFormDmFilterInput());
			$this->setValidator('position', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));
		}


		if($this->needsWidget('widgets_list')){
			$this->setWidget('widgets_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmWidget', 'expanded' => true)));
			$this->setValidator('widgets_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmWidget', 'required' => false)));
		}

		if($this->needsWidget('area_list')){
			$this->setWidget('area_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmArea', 'expanded' => false)));
			$this->setValidator('area_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmArea', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_zone_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmZone';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'dm_area_id' => 'ForeignKey',
      'css_class'  => 'Text',
      'width'      => 'Text',
      'position'   => 'Number',
    );
  }
}
