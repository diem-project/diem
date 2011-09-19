<?php

/**
 * DmPageView filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmPageViewFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmPageView', 'column' => 'id')));
		}
		if($this->needsWidget('module')){
			$this->setWidget('module', new sfWidgetFormDmFilterInput());
			$this->setValidator('module', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('action')){
			$this->setWidget('action', new sfWidgetFormDmFilterInput());
			$this->setValidator('action', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}


		if($this->needsWidget('areas_list')){
			$this->setWidget('areas_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmArea', 'expanded' => true)));
			$this->setValidator('areas_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmArea', 'required' => false)));
		}

		if($this->needsWidget('layout_list')){
			$this->setWidget('layout_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmLayout', 'expanded' => false)));
			$this->setValidator('layout_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmLayout', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_page_view_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmPageView';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'module'       => 'Text',
      'action'       => 'Text',
      'dm_layout_id' => 'ForeignKey',
    );
  }
}
