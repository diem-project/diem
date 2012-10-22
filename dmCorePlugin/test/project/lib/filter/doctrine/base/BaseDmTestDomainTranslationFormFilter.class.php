<?php

/**
 * DmTestDomainTranslation filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTestDomainTranslationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('title')){
			$this->setWidget('title', new sfWidgetFormDmFilterInput());
			$this->setValidator('title', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('is_active')){
			$this->setWidget('is_active', new sfWidgetFormChoice(array('choices' => array('' => $this->getI18n()->__('yes or no', array(), 'dm'), 1 => $this->getI18n()->__('yes', array(), 'dm'), 0 => $this->getI18n()->__('no', array(), 'dm')))));
			$this->setValidator('is_active', new sfValidatorBoolean());
		}
		if($this->needsWidget('lang')){
			$this->setWidget('lang', new sfWidgetFormDmFilterInput());
			$this->setValidator('lang', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmTestDomainTranslation', 'column' => 'lang')));
		}



		if($this->needsWidget('dm_test_domain_list')){
			$this->setWidget('dm_test_domain_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmTestDomain', 'expanded' => false)));
			$this->setValidator('dm_test_domain_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmTestDomain', 'required' => true)));
		}
		if($this->needsWidget('created_by_list')){
			$this->setWidget('created_by_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'expanded' => false)));
			$this->setValidator('created_by_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'required' => true)));
		}
		if($this->needsWidget('updated_by_list')){
			$this->setWidget('updated_by_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'expanded' => false)));
			$this->setValidator('updated_by_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_test_domain_translation_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmTestDomainTranslation';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'title'      => 'Text',
      'is_active'  => 'Boolean',
      'lang'       => 'Text',
      'created_by' => 'ForeignKey',
      'updated_by' => 'ForeignKey',
    );
  }
}
