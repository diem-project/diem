<?php

/**
 * DmError filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmErrorFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'klass'       => new sfWidgetFormFilterInput(),
      'name'        => new sfWidgetFormFilterInput(),
      'description' => new sfWidgetFormFilterInput(),
      'module'      => new sfWidgetFormFilterInput(),
      'action'      => new sfWidgetFormFilterInput(),
      'uri'         => new sfWidgetFormFilterInput(),
      'env'         => new sfWidgetFormFilterInput(),
      'created_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormInputText(array(), array("class" => "datepicker_me")), 'to_date' => new sfWidgetFormInputText(array(), array("class" => "datepicker_me")), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'klass'       => new sfValidatorPass(array('required' => false)),
      'name'        => new sfValidatorPass(array('required' => false)),
      'description' => new sfValidatorPass(array('required' => false)),
      'module'      => new sfValidatorPass(array('required' => false)),
      'action'      => new sfValidatorPass(array('required' => false)),
      'uri'         => new sfValidatorPass(array('required' => false)),
      'env'         => new sfValidatorPass(array('required' => false)),
      'created_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('dm_error_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmError';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'klass'       => 'Text',
      'name'        => 'Text',
      'description' => 'Text',
      'module'      => 'Text',
      'action'      => 'Text',
      'uri'         => 'Text',
      'env'         => 'Text',
      'created_at'  => 'Date',
    );
  }
}
