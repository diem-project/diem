<?php

/**
 * DmTagTranslation form base class.
 *
 * @method DmTagTranslation getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTagTranslationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'   => new sfWidgetFormInputHidden(),
      'name' => new sfWidgetFormInputText(),
      'lang' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'id'   => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'name' => new sfValidatorString(array('max_length' => 255)),
      'lang' => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'lang', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmTagTranslation', 'column' => array('name')))
    );

    $this->widgetSchema->setNameFormat('dm_tag_translation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmTagTranslation';
  }

}
