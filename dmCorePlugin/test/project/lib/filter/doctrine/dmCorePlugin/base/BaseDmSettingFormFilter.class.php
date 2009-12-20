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
    $this->setWidgets(array(
      'name'        => new sfWidgetFormFilterInput(),
      'type'        => new sfWidgetFormChoice(array('choices' => array('' => '', 'text' => 'text', 'boolean' => 'boolean', 'select' => 'select', 'textarea' => 'textarea', 'number' => 'number'))),
      'params'      => new sfWidgetFormFilterInput(),
      'group_name'  => new sfWidgetFormFilterInput(),
      'credentials' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'name'        => new sfValidatorPass(array('required' => false)),
      'type'        => new sfValidatorChoice(array('required' => false, 'choices' => array('text' => 'text', 'boolean' => 'boolean', 'select' => 'select', 'textarea' => 'textarea', 'number' => 'number'))),
      'params'      => new sfValidatorPass(array('required' => false)),
      'group_name'  => new sfValidatorPass(array('required' => false)),
      'credentials' => new sfValidatorPass(array('required' => false)),
    ));

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
      'id'          => 'Number',
      'name'        => 'Text',
      'type'        => 'Enum',
      'params'      => 'Text',
      'group_name'  => 'Text',
      'credentials' => 'Text',
    );
  }
}
