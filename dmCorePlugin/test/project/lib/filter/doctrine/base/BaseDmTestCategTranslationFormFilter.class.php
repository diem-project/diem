<?php

/**
 * DmTestCategTranslation filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTestCategTranslationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormDmFilterInput());
			$this->setValidator('name', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('is_active')){
			$this->setWidget('is_active', new sfWidgetFormChoice(array('choices' => array('' => $this->getI18n()->__('yes or no', array(), 'dm'), 1 => $this->getI18n()->__('yes', array(), 'dm'), 0 => $this->getI18n()->__('no', array(), 'dm')))));
			$this->setValidator('is_active', new sfValidatorBoolean());
		}
		if($this->needsWidget('lang')){
			$this->setWidget('lang', new sfWidgetFormDmFilterInput());
			$this->setValidator('lang', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmTestCategTranslation', 'column' => 'lang')));
		}



		if($this->needsWidget('dm_test_categ_list')){
			$this->setWidget('dm_test_categ_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmTestCateg', 'expanded' => false)));
			$this->setValidator('dm_test_categ_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmTestCateg', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_test_categ_translation_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmTestCategTranslation';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'name'      => 'Text',
      'is_active' => 'Boolean',
      'lang'      => 'Text',
    );
  }
}
