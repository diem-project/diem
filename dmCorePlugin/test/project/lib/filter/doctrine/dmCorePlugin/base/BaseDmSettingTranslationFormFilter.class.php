<?php

/**
 * DmSettingTranslation filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmSettingTranslationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('description')){
			$this->setWidget('description', new sfWidgetFormDmFilterInput());
			$this->setValidator('description', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('value')){
			$this->setWidget('value', new sfWidgetFormDmFilterInput());
			$this->setValidator('value', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('default_value')){
			$this->setWidget('default_value', new sfWidgetFormDmFilterInput());
			$this->setValidator('default_value', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('lang')){
			$this->setWidget('lang', new sfWidgetFormDmFilterInput());
			$this->setValidator('lang', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmSettingTranslation', 'column' => 'lang')));
		}



		if($this->needsWidget('dm_setting_list')){
			$this->setWidget('dm_setting_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmSetting', 'expanded' => false)));
			$this->setValidator('dm_setting_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmSetting', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_setting_translation_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmSettingTranslation';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'description'   => 'Text',
      'value'         => 'Text',
      'default_value' => 'Text',
      'lang'          => 'Text',
    );
  }
}
