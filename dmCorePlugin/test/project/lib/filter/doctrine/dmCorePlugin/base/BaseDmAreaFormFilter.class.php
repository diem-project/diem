<?php

/**
 * DmArea filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmAreaFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'dm_layout_id'    => new sfWidgetFormDoctrineChoice(array('model' => 'DmLayout', 'add_empty' => true)),
      'dm_page_view_id' => new sfWidgetFormDoctrineChoice(array('model' => 'DmPageView', 'add_empty' => true)),
      'type'            => new sfWidgetFormChoice(array('choices' => array('' => '', 'content' => 'content', 'top' => 'top', 'bottom' => 'bottom', 'left' => 'left', 'right' => 'right'))),
    ));

    $this->setValidators(array(
      'dm_layout_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Layout'), 'column' => 'id')),
      'dm_page_view_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('PageView'), 'column' => 'id')),
      'type'            => new sfValidatorChoice(array('required' => false, 'choices' => array('content' => 'content', 'top' => 'top', 'bottom' => 'bottom', 'left' => 'left', 'right' => 'right'))),
    ));

    $this->widgetSchema->setNameFormat('dm_area_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmArea';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'dm_layout_id'    => 'ForeignKey',
      'dm_page_view_id' => 'ForeignKey',
      'type'            => 'Enum',
    );
  }
}
