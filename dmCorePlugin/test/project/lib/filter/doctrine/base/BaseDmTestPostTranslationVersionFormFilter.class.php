<?php

/**
 * DmTestPostTranslationVersion filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTestPostTranslationVersionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'title'   => new sfWidgetFormFilterInput(),
      'excerpt' => new sfWidgetFormFilterInput(),
      'body'    => new sfWidgetFormFilterInput(),
      'url'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'title'   => new sfValidatorPass(array('required' => false)),
      'excerpt' => new sfValidatorPass(array('required' => false)),
      'body'    => new sfValidatorPass(array('required' => false)),
      'url'     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dm_test_post_translation_version_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmTestPostTranslationVersion';
  }

  public function getFields()
  {
    return array(
      'id'      => 'Number',
      'lang'    => 'Text',
      'title'   => 'Text',
      'excerpt' => 'Text',
      'body'    => 'Text',
      'url'     => 'Text',
      'version' => 'Number',
    );
  }
}
