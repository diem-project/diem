<?php

/**
 * DmTestPostTranslation form base class.
 *
 * @method DmTestPostTranslation getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDmTestPostTranslationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormInputHidden(),
      'title'     => new sfWidgetFormInputText(),
      'excerpt'   => new sfWidgetFormTextarea(),
      'body'      => new sfWidgetFormTextarea(),
      'url'       => new sfWidgetFormInputText(),
      'is_active' => new sfWidgetFormInputCheckbox(),
      'lang'      => new sfWidgetFormInputHidden(),
      'version'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'title'     => new sfValidatorString(array('max_length' => 255)),
      'excerpt'   => new sfValidatorString(array('max_length' => 800, 'required' => false)),
      'body'      => new sfValidatorString(array('required' => false)),
      'url'       => new dmValidatorLinkUrl(array('max_length' => 255, 'required' => false)),
      'is_active' => new sfValidatorBoolean(array('required' => false)),
      'lang'      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('lang')), 'empty_value' => $this->getObject()->get('lang'), 'required' => false)),
      'version'   => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dm_test_post_translation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmTestPostTranslation';
  }

}
