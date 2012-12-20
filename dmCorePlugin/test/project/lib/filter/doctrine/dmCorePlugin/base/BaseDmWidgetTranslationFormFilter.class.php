<?php

/**
 * DmWidgetTranslation filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmWidgetTranslationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('value')){
			$this->setWidget('value', new sfWidgetFormDmFilterInput());
			$this->setValidator('value', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('lang')){
			$this->setWidget('lang', new sfWidgetFormDmFilterInput());
			$this->setValidator('lang', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmWidgetTranslation', 'column' => 'lang')));
		}



		if($this->needsWidget('dm_widget_list')){
			$this->setWidget('dm_widget_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmWidget', 'expanded' => false)));
			$this->setValidator('dm_widget_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmWidget', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_widget_translation_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmWidgetTranslation';
  }

  public function getFields()
  {
    return array(
      'id'    => 'Number',
      'value' => 'Text',
      'lang'  => 'Text',
    );
  }
}
