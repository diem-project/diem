<?php

/**
 * DmLock filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmLockFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'user_id'   => new sfWidgetFormDoctrineChoice(array('model' => 'DmUser', 'add_empty' => true)),
      'user_name' => new sfWidgetFormFilterInput(),
      'module'    => new sfWidgetFormFilterInput(),
      'action'    => new sfWidgetFormFilterInput(),
      'record_id' => new sfWidgetFormFilterInput(),
      'time'      => new sfWidgetFormFilterInput(),
      'app'       => new sfWidgetFormFilterInput(),
      'url'       => new sfWidgetFormFilterInput(),
      'culture'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'user_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'user_name' => new sfValidatorPass(array('required' => false)),
      'module'    => new sfValidatorPass(array('required' => false)),
      'action'    => new sfValidatorPass(array('required' => false)),
      'record_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'time'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'app'       => new sfValidatorPass(array('required' => false)),
      'url'       => new sfValidatorPass(array('required' => false)),
      'culture'   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dm_lock_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmLock';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'user_id'   => 'ForeignKey',
      'user_name' => 'Text',
      'module'    => 'Text',
      'action'    => 'Text',
      'record_id' => 'Number',
      'time'      => 'Number',
      'app'       => 'Text',
      'url'       => 'Text',
      'culture'   => 'Text',
    );
  }
}
