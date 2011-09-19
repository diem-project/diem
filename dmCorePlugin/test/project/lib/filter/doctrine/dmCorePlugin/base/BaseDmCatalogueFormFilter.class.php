<?php

/**
 * DmCatalogue filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmCatalogueFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmCatalogue', 'column' => 'id')));
		}
		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormDmFilterInput());
			$this->setValidator('name', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('source_lang')){
			$this->setWidget('source_lang', new sfWidgetFormDmFilterInput());
			$this->setValidator('source_lang', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('target_lang')){
			$this->setWidget('target_lang', new sfWidgetFormDmFilterInput());
			$this->setValidator('target_lang', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}


		if($this->needsWidget('units_list')){
			$this->setWidget('units_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTransUnit', 'expanded' => true)));
			$this->setValidator('units_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTransUnit', 'required' => false)));
		}


    

    $this->widgetSchema->setNameFormat('dm_catalogue_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmCatalogue';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'name'        => 'Text',
      'source_lang' => 'Text',
      'target_lang' => 'Text',
    );
  }
}
