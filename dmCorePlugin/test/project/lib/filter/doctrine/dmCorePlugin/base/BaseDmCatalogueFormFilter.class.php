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
    $this->setWidgets(array(
      'name'        => new sfWidgetFormDmFilterInput(),
      'source_lang' => new sfWidgetFormDmFilterInput(),
      'target_lang' => new sfWidgetFormDmFilterInput(),
    ));

    $this->setValidators(array(
      'name'        => new sfValidatorPass(array('required' => false)),
      'source_lang' => new sfValidatorPass(array('required' => false)),
      'target_lang' => new sfValidatorPass(array('required' => false)),
    ));
    

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
