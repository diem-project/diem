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
      'title'     => new sfWidgetFormDmFilterInput(),
      'excerpt'   => new sfWidgetFormDmFilterInput(),
      'body'      => new sfWidgetFormDmFilterInput(),
      'url'       => new sfWidgetFormDmFilterInput(),
      'is_active' => new sfWidgetFormChoice(array('choices' => array('' => $this->getI18n()->__('yes or no', array(), 'dm'), 1 => $this->getI18n()->__('yes', array(), 'dm'), 0 => $this->getI18n()->__('no', array(), 'dm')))),
    ));

    $this->setValidators(array(
      'title'     => new sfValidatorPass(array('required' => false)),
      'excerpt'   => new sfValidatorPass(array('required' => false)),
      'body'      => new sfValidatorPass(array('required' => false)),
      'url'       => new sfValidatorPass(array('required' => false)),
      'is_active' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
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
      'id'        => 'Number',
      'lang'      => 'Text',
      'title'     => 'Text',
      'excerpt'   => 'Text',
      'body'      => 'Text',
      'url'       => 'Text',
      'is_active' => 'Boolean',
      'version'   => 'Number',
    );
  }
}
