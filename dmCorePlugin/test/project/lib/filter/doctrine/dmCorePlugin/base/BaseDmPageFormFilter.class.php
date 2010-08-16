<?php

/**
 * DmPage filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmPageFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'module'       => new sfWidgetFormDmFilterInput(),
      'action'       => new sfWidgetFormDmFilterInput(),
      'template'     => new sfWidgetFormDmFilterInput(),
      'record_id'    => new sfWidgetFormDmFilterInput(),
      'credentials'  => new sfWidgetFormDmFilterInput(),
      'lft'          => new sfWidgetFormDmFilterInput(),
      'rgt'          => new sfWidgetFormDmFilterInput(),
      'level'        => new sfWidgetFormDmFilterInput(),
    ));

    $this->setValidators(array(
      'module'       => new sfValidatorPass(array('required' => false)),
      'action'       => new sfValidatorPass(array('required' => false)),
      'template'     => new sfValidatorPass(array('required' => false)),
      'record_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'credentials'  => new sfValidatorPass(array('required' => false)),
      'lft'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'rgt'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'level'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));
    
    $this->mergeI18nForm();


    $this->widgetSchema->setNameFormat('dm_page_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmPage';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'module'       => 'Text',
      'action'       => 'Text',
      'template'     => 'Text',
      'record_id'    => 'Number',
      'credentials'  => 'Text',
      'lft'          => 'Number',
      'rgt'          => 'Number',
      'level'        => 'Number',
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
