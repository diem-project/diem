<?php

/**
 * DmMailTemplateTranslation form base class.
 *
 * @method DmMailTemplateTranslation getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmMailTemplateTranslationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'description' => new sfWidgetFormTextarea(),
      'title'       => new sfWidgetFormTextarea(),
      'body'        => new sfWidgetFormTextarea(),
      'from_email'  => new sfWidgetFormTextarea(),
      'to_email'    => new sfWidgetFormTextarea(),
      'is_html'     => new sfWidgetFormInputCheckbox(),
      'is_active'   => new sfWidgetFormInputCheckbox(),
      'lang'        => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'description' => new sfValidatorString(array('max_length' => 5000, 'required' => false)),
      'title'       => new sfValidatorString(array('max_length' => 5000, 'required' => false)),
      'body'        => new sfValidatorString(array('required' => false)),
      'from_email'  => new sfValidatorString(array('max_length' => 5000, 'required' => false)),
      'to_email'    => new sfValidatorString(array('max_length' => 5000, 'required' => false)),
      'is_html'     => new sfValidatorBoolean(array('required' => false)),
      'is_active'   => new sfValidatorBoolean(array('required' => false)),
      'lang'        => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'lang', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dm_mail_template_translation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmMailTemplateTranslation';
  }

}
