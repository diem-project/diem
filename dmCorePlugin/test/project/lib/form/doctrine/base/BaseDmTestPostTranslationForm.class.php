<?php

/**
 * DmTestPostTranslation form base class.
 *
 * @method DmTestPostTranslation getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTestPostTranslationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'      => new sfWidgetFormInputHidden(),
      'title'   => new sfWidgetFormInputText(),
      'excerpt' => new sfWidgetFormTextarea(),
      'body'    => new sfWidgetFormTextarea(),
      'url'     => new sfWidgetFormInputText(),
      'lang'    => new sfWidgetFormInputHidden(),
      'version' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'      => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'title'   => new sfValidatorString(array('max_length' => 255)),
      'excerpt' => new sfValidatorString(array('max_length' => 800, 'required' => false)),
      'body'    => new sfValidatorString(array('required' => false)),
      'url'     => new dmValidatorLinkUrl(array('max_length' => 255, 'required' => false)),
      'lang'    => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'lang', 'required' => false)),
      'version' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmTestPostTranslation', 'column' => array('title')))
    );

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
