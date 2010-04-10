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
      'php_class'   => new sfWidgetFormDmFilterInput(),
      'name'        => new sfWidgetFormDmFilterInput(),
      'description' => new sfWidgetFormDmFilterInput(),
      'module'      => new sfWidgetFormDmFilterInput(),
      'action'      => new sfWidgetFormDmFilterInput(),
      'uri'         => new sfWidgetFormDmFilterInput(),
      'env'         => new sfWidgetFormDmFilterInput(),
      'created_at'  => new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))),
    ));

    $this->setValidators(array(
      'php_class'   => new sfValidatorPass(array('required' => false)),
      'name'        => new sfValidatorPass(array('required' => false)),
      'description' => new sfValidatorPass(array('required' => false)),
      'module'      => new sfValidatorPass(array('required' => false)),
      'action'      => new sfValidatorPass(array('required' => false)),
      'uri'         => new sfValidatorPass(array('required' => false)),
      'env'         => new sfValidatorPass(array('required' => false)),
      'created_at'  => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['created_at']->getOption('choices')))),
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
      'php_class'   => 'Text',
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
