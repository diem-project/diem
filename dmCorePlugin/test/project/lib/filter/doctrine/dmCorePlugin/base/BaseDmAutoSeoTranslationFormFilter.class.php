<?php

/**
 * DmAutoSeoTranslation filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmAutoSeoTranslationFormFilter extends BaseFormFilterDoctrine
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
		if($this->needsWidget('strip_words')){
			$this->setWidget('strip_words', new sfWidgetFormDmFilterInput());
			$this->setValidator('strip_words', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('lang')){
			$this->setWidget('lang', new sfWidgetFormDmFilterInput());
			$this->setValidator('lang', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmAutoSeoTranslation', 'column' => 'lang')));
		}



		if($this->needsWidget('dm_auto_seo_list')){
			$this->setWidget('dm_auto_seo_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmAutoSeo', 'expanded' => false)));
			$this->setValidator('dm_auto_seo_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmAutoSeo', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_auto_seo_translation_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmAutoSeoTranslation';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'slug'        => 'Text',
      'name'        => 'Text',
      'title'       => 'Text',
      'h1'          => 'Text',
      'description' => 'Text',
      'keywords'    => 'Text',
      'strip_words' => 'Text',
      'lang'        => 'Text',
    );
  }
}
