<?php

/**
 * DmSettingTranslation form base class.
 *
 * @method DmSettingTranslation getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmSettingTranslationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'description'   => new sfWidgetFormInputText(),
      'value'         => new sfWidgetFormTextarea(),
      'default_value' => new sfWidgetFormTextarea(),
      'lang'          => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'description'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'value'         => new sfValidatorString(array('max_length' => 60000, 'required' => false)),
      'default_value' => new sfValidatorString(array('max_length' => 60000, 'required' => false)),
      'lang'          => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'lang', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dm_setting_translation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmSettingTranslation';
  }

}
