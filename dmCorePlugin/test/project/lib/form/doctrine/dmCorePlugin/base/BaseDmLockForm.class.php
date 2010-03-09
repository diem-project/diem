<?php

/**
 * DmLock form base class.
 *
 * @method DmLock getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmLockForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormInputHidden(),
      'user_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'user_name' => new sfWidgetFormInputText(),
      'module'    => new sfWidgetFormInputText(),
      'action'    => new sfWidgetFormInputText(),
      'record_id' => new sfWidgetFormInputText(),
      'time'      => new sfWidgetFormInputText(),
      'app'       => new sfWidgetFormInputText(),
      'url'       => new sfWidgetFormInputText(),
      'culture'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'user_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'user_name' => new sfValidatorString(array('max_length' => 255)),
      'module'    => new sfValidatorString(array('max_length' => 127)),
      'action'    => new sfValidatorString(array('max_length' => 127)),
      'record_id' => new sfValidatorInteger(array('required' => false)),
      'time'      => new sfValidatorInteger(),
      'app'       => new sfValidatorString(array('max_length' => 127)),
      'url'       => new sfValidatorString(array('max_length' => 255)),
      'culture'   => new sfValidatorString(array('max_length' => 15)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmLock', 'column' => array('user_id', 'module', 'action', 'record_id')))
    );

    $this->widgetSchema->setNameFormat('dm_lock[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmLock';
  }

}
