<?php

/**
 * DmArea filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmAreaFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmArea', 'column' => 'id')));
		}
		if($this->needsWidget('type')){
			$this->setWidget('type', new sfWidgetFormDmFilterInput());
			$this->setValidator('type', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}


		if($this->needsWidget('zones_list')){
			$this->setWidget('zones_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmZone', 'expanded' => true)));
			$this->setValidator('zones_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmZone', 'required' => false)));
		}

		if($this->needsWidget('layout_list')){
			$this->setWidget('layout_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmLayout', 'expanded' => false)));
			$this->setValidator('layout_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmLayout', 'required' => true)));
		}
		if($this->needsWidget('page_view_list')){
			$this->setWidget('page_view_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmPageView', 'expanded' => false)));
			$this->setValidator('page_view_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmPageView', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_area_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmArea';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'dm_layout_id'    => 'ForeignKey',
      'dm_page_view_id' => 'ForeignKey',
      'type'            => 'Text',
    );
  }
}
