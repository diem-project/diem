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
      'module'     => new sfWidgetFormDmFilterInput(),
      'action'     => new sfWidgetFormDmFilterInput(),
      'css_class'  => new sfWidgetFormDmFilterInput(),
      'position'   => new sfWidgetFormDmFilterInput(),
      'updated_at' => new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))),
    ));

    $this->setValidators(array(
      'dm_zone_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Zone'), 'column' => 'id')),
      'module'     => new sfValidatorPass(array('required' => false)),
      'action'     => new sfValidatorPass(array('required' => false)),
      'css_class'  => new sfValidatorPass(array('required' => false)),
      'position'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'updated_at' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['updated_at']->getOption('choices')))),
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
