<?php

/**
 * DmTransUnit filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTransUnitFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'dm_catalogue_id' => new sfWidgetFormDoctrineChoice(array('model' => 'DmCatalogue', 'add_empty' => true)),
      'source'          => new sfWidgetFormDmFilterInput(),
      'target'          => new sfWidgetFormDmFilterInput(),
      'meta'            => new sfWidgetFormDmFilterInput(),
      'created_at'      => new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))),
      'updated_at'      => new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))),
    ));

    $this->setValidators(array(
      'dm_catalogue_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('DmCatalogue'), 'column' => 'id')),
      'source'          => new sfValidatorPass(array('required' => false)),
      'target'          => new sfValidatorPass(array('required' => false)),
      'meta'            => new sfValidatorPass(array('required' => false)),
      'created_at'      => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['created_at']->getOption('choices')))),
      'updated_at'      => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['updated_at']->getOption('choices')))),
    ));
    

    $this->widgetSchema->setNameFormat('dm_trans_unit_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmTransUnit';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'dm_catalogue_id' => 'ForeignKey',
      'source'          => 'Text',
      'target'          => 'Text',
      'meta'            => 'Text',
      'created_at'      => 'Date',
      'updated_at'      => 'Date',
    );
  }
}
