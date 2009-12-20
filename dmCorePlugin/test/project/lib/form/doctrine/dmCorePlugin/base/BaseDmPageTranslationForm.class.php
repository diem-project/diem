<?php

/**
 * DmPageTranslation form base class.
 *
 * @method DmPageTranslation getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmPageTranslationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'slug'         => new sfWidgetFormInputText(),
      'name'         => new sfWidgetFormInputText(),
      'title'        => new sfWidgetFormInputText(),
      'h1'           => new sfWidgetFormInputText(),
      'description'  => new sfWidgetFormInputText(),
      'keywords'     => new sfWidgetFormInputText(),
      'auto_mod'     => new sfWidgetFormInputText(),
      'is_active'    => new sfWidgetFormInputCheckbox(),
      'is_secure'    => new sfWidgetFormInputCheckbox(),
      'is_indexable' => new sfWidgetFormInputCheckbox(),
      'lang'         => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'slug'         => new sfValidatorString(array('max_length' => 255)),
      'name'         => new sfValidatorString(array('max_length' => 255)),
      'title'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'h1'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'description'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'keywords'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'auto_mod'     => new sfValidatorString(array('max_length' => 6, 'required' => false)),
      'is_active'    => new sfValidatorBoolean(array('required' => false)),
      'is_secure'    => new sfValidatorBoolean(array('required' => false)),
      'is_indexable' => new sfValidatorBoolean(array('required' => false)),
      'lang'         => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'lang', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dm_page_translation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmPageTranslation';
  }

}
