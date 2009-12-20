<?php

/**
 * DmAutoSeoTranslation form base class.
 *
 * @method DmAutoSeoTranslation getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmAutoSeoTranslationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'slug'        => new sfWidgetFormInputText(),
      'name'        => new sfWidgetFormInputText(),
      'title'       => new sfWidgetFormInputText(),
      'h1'          => new sfWidgetFormInputText(),
      'description' => new sfWidgetFormInputText(),
      'keywords'    => new sfWidgetFormInputText(),
      'strip_words' => new sfWidgetFormTextarea(),
      'lang'        => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'slug'        => new sfValidatorString(array('max_length' => 255)),
      'name'        => new sfValidatorString(array('max_length' => 255)),
      'title'       => new sfValidatorString(array('max_length' => 255)),
      'h1'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'description' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'keywords'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'strip_words' => new sfValidatorString(array('max_length' => 10000, 'required' => false)),
      'lang'        => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'lang', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dm_auto_seo_translation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmAutoSeoTranslation';
  }

}
