<?php

/**
 * DmSetting filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmSettingFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmSetting', 'column' => 'id')));
		}
		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormDmFilterInput());
			$this->setValidator('name', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('type')){
			$this->setWidget('type', new sfWidgetFormChoice(array('multiple' => true, 'choices' => array('' => '', 'text' => 'text', 'boolean' => 'boolean', 'select' => 'select', 'textarea' => 'textarea', 'number' => 'number', 'datetime' => 'datetime'))));
			$this->setValidator('type', new sfValidatorChoice(array('required' => false, 'multiple' => true , 'choices' => array('text' => 'text', 'boolean' => 'boolean', 'select' => 'select', 'textarea' => 'textarea', 'number' => 'number', 'datetime' => 'datetime'))));
		}
		if($this->needsWidget('params')){
			$this->setWidget('params', new sfWidgetFormDmFilterInput());
			$this->setValidator('params', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('group_name')){
			$this->setWidget('group_name', new sfWidgetFormDmFilterInput());
			$this->setValidator('group_name', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('credentials')){
			$this->setWidget('credentials', new sfWidgetFormDmFilterInput());
			$this->setValidator('credentials', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}




    
    $this->mergeI18nForm();


    $this->widgetSchema->setNameFormat('dm_setting_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmSetting';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'name'          => 'Text',
      'type'          => 'Enum',
      'params'        => 'Text',
      'group_name'    => 'Text',
      'credentials'   => 'Text',
      'id'            => 'Number',
      'description'   => 'Text',
      'value'         => 'Text',
      'default_value' => 'Text',
      'lang'          => 'Text',
    );
  }
}
