<?php

/**
 * DmTestPostTranslation filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTestPostTranslationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


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
		if($this->needsWidget('lang')){
			$this->setWidget('lang', new sfWidgetFormDmFilterInput());
			$this->setValidator('lang', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmTestPostTranslation', 'column' => 'lang')));
		}
		if($this->needsWidget('version')){
			$this->setWidget('version', new sfWidgetFormDmFilterInput());
			$this->setValidator('version', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));
		}


		if($this->needsWidget('version_list')){
			$this->setWidget('version_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPostTranslationVersion', 'expanded' => true)));
			$this->setValidator('version_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPostTranslationVersion', 'required' => false)));
		}

		if($this->needsWidget('dm_test_post_list')){
			$this->setWidget('dm_test_post_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmTestPost', 'expanded' => false)));
			$this->setValidator('dm_test_post_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmTestPost', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_test_post_translation_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmTestPostTranslation';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'title'     => 'Text',
      'excerpt'   => 'Text',
      'body'      => 'Text',
      'url'       => 'Text',
      'is_active' => 'Boolean',
      'lang'      => 'Text',
      'version'   => 'Number',
    );
  }
}
