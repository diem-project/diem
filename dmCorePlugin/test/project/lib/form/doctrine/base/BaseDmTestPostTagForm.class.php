<?php

/**
 * DmTestPostTag form base class.
 *
 * @method DmTestPostTag getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTestPostTagForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'dm_test_post_id' => new sfWidgetFormInputHidden(),
      'dm_test_tag_id'  => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'dm_test_post_id' => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'dm_test_post_id', 'required' => false)),
      'dm_test_tag_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'dm_test_tag_id', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dm_test_post_tag[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmTestPostTag';
  }

}
