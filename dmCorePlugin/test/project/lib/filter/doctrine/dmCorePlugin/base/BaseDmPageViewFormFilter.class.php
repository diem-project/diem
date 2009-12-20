<?php

/**
 * DmPageView filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmPageViewFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'module'       => new sfWidgetFormFilterInput(),
      'action'       => new sfWidgetFormFilterInput(),
      'dm_layout_id' => new sfWidgetFormDoctrineChoice(array('model' => 'DmLayout', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'module'       => new sfValidatorPass(array('required' => false)),
      'action'       => new sfValidatorPass(array('required' => false)),
      'dm_layout_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Layout'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('dm_page_view_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmPageView';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'module'       => 'Text',
      'action'       => 'Text',
      'dm_layout_id' => 'ForeignKey',
    );
  }
}
