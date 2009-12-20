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
    $this->setWidgets(array(
      'dm_zone_id' => new sfWidgetFormDoctrineChoice(array('model' => 'DmZone', 'add_empty' => true)),
      'module'     => new sfWidgetFormFilterInput(),
      'action'     => new sfWidgetFormFilterInput(),
      'css_class'  => new sfWidgetFormFilterInput(),
      'position'   => new sfWidgetFormFilterInput(),
      'updated_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormInputText(array(), array("class" => "datepicker_me")), 'to_date' => new sfWidgetFormInputText(array(), array("class" => "datepicker_me")), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'dm_zone_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Zone'), 'column' => 'id')),
      'module'     => new sfValidatorPass(array('required' => false)),
      'action'     => new sfValidatorPass(array('required' => false)),
      'css_class'  => new sfValidatorPass(array('required' => false)),
      'position'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'updated_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

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
    );
  }
}
