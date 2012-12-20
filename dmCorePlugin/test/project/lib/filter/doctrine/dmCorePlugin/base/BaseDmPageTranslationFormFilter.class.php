<?php

/**
 * DmPageTranslation filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmPageTranslationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('slug')){
			$this->setWidget('slug', new sfWidgetFormDmFilterInput());
			$this->setValidator('slug', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormDmFilterInput());
			$this->setValidator('name', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('title')){
			$this->setWidget('title', new sfWidgetFormDmFilterInput());
			$this->setValidator('title', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('h1')){
			$this->setWidget('h1', new sfWidgetFormDmFilterInput());
			$this->setValidator('h1', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('description')){
			$this->setWidget('description', new sfWidgetFormDmFilterInput());
			$this->setValidator('description', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('keywords')){
			$this->setWidget('keywords', new sfWidgetFormDmFilterInput());
			$this->setValidator('keywords', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('auto_mod')){
			$this->setWidget('auto_mod', new sfWidgetFormDmFilterInput());
			$this->setValidator('auto_mod', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('is_active')){
			$this->setWidget('is_active', new sfWidgetFormChoice(array('choices' => array('' => $this->getI18n()->__('yes or no', array(), 'dm'), 1 => $this->getI18n()->__('yes', array(), 'dm'), 0 => $this->getI18n()->__('no', array(), 'dm')))));
			$this->setValidator('is_active', new sfValidatorBoolean());
		}
		if($this->needsWidget('is_secure')){
			$this->setWidget('is_secure', new sfWidgetFormChoice(array('choices' => array('' => $this->getI18n()->__('yes or no', array(), 'dm'), 1 => $this->getI18n()->__('yes', array(), 'dm'), 0 => $this->getI18n()->__('no', array(), 'dm')))));
			$this->setValidator('is_secure', new sfValidatorBoolean());
		}
		if($this->needsWidget('is_indexable')){
			$this->setWidget('is_indexable', new sfWidgetFormChoice(array('choices' => array('' => $this->getI18n()->__('yes or no', array(), 'dm'), 1 => $this->getI18n()->__('yes', array(), 'dm'), 0 => $this->getI18n()->__('no', array(), 'dm')))));
			$this->setValidator('is_indexable', new sfValidatorBoolean());
		}
		if($this->needsWidget('lang')){
			$this->setWidget('lang', new sfWidgetFormDmFilterInput());
			$this->setValidator('lang', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmPageTranslation', 'column' => 'lang')));
		}



		if($this->needsWidget('dm_page_list')){
			$this->setWidget('dm_page_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmPage', 'expanded' => false)));
			$this->setValidator('dm_page_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmPage', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_page_translation_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmPageTranslation';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'slug'         => 'Text',
      'name'         => 'Text',
      'title'        => 'Text',
      'h1'           => 'Text',
      'description'  => 'Text',
      'keywords'     => 'Text',
      'auto_mod'     => 'Text',
      'is_active'    => 'Boolean',
      'is_secure'    => 'Boolean',
      'is_indexable' => 'Boolean',
      'lang'         => 'Text',
    );
  }
}
