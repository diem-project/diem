<?php

/**
 * DmLayout filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmLayoutFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmLayout', 'column' => 'id')));
		}
		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormDmFilterInput());
			$this->setValidator('name', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('template')){
			$this->setWidget('template', new sfWidgetFormDmFilterInput());
			$this->setValidator('template', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('css_class')){
			$this->setWidget('css_class', new sfWidgetFormDmFilterInput());
			$this->setValidator('css_class', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}


		if($this->needsWidget('page_views_list')){
			$this->setWidget('page_views_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmPageView', 'expanded' => true)));
			$this->setValidator('page_views_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmPageView', 'required' => false)));
		}
		if($this->needsWidget('areas_list')){
			$this->setWidget('areas_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmArea', 'expanded' => true)));
			$this->setValidator('areas_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmArea', 'required' => false)));
		}


    

    $this->widgetSchema->setNameFormat('dm_layout_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmLayout';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'name'      => 'Text',
      'template'  => 'Text',
      'css_class' => 'Text',
    );
  }
}
