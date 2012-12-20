<?php

/**
 * DmTestTagTranslation filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTestTagTranslationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormDmFilterInput());
			$this->setValidator('name', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('slug')){
			$this->setWidget('slug', new sfWidgetFormDmFilterInput());
			$this->setValidator('slug', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('lang')){
			$this->setWidget('lang', new sfWidgetFormDmFilterInput());
			$this->setValidator('lang', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmTestTagTranslation', 'column' => 'lang')));
		}



		if($this->needsWidget('dm_test_tag_list')){
			$this->setWidget('dm_test_tag_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmTestTag', 'expanded' => false)));
			$this->setValidator('dm_test_tag_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmTestTag', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_test_tag_translation_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmTestTagTranslation';
  }

  public function getFields()
  {
    return array(
      'id'   => 'Number',
      'name' => 'Text',
      'slug' => 'Text',
      'lang' => 'Text',
    );
  }
}
