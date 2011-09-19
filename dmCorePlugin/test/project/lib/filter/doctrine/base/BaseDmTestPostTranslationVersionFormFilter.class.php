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


		if($this->needsWidget('lang')){
			$this->setWidget('lang', new sfWidgetFormDmFilterInput());
			$this->setValidator('lang', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmTestPostTranslationVersion', 'column' => 'lang')));
		}
		if($this->needsWidget('title')){
			$this->setWidget('title', new sfWidgetFormDmFilterInput());
			$this->setValidator('title', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('excerpt')){
			$this->setWidget('excerpt', new sfWidgetFormDmFilterInput());
			$this->setValidator('excerpt', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('body')){
			$this->setWidget('body', new sfWidgetFormDmFilterInput());
			$this->setValidator('body', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('url')){
			$this->setWidget('url', new sfWidgetFormDmFilterInput());
			$this->setValidator('url', new dmValidatorLinkUrl(array('required' => false)));
		}
		if($this->needsWidget('is_active')){
			$this->setWidget('is_active', new sfWidgetFormChoice(array('choices' => array('' => $this->getI18n()->__('yes or no', array(), 'dm'), 1 => $this->getI18n()->__('yes', array(), 'dm'), 0 => $this->getI18n()->__('no', array(), 'dm')))));
			$this->setValidator('is_active', new sfValidatorBoolean());
		}
		if($this->needsWidget('version')){
			$this->setWidget('version', new sfWidgetFormDmFilterInput());
			$this->setValidator('version', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmTestPostTranslationVersion', 'column' => 'version')));
		}



		if($this->needsWidget('dm_test_post_translation_list')){
			$this->setWidget('dm_test_post_translation_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmTestPostTranslation', 'expanded' => false)));
			$this->setValidator('dm_test_post_translation_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmTestPostTranslation', 'required' => true)));
		}

    

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
