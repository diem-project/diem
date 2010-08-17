<?php

/**
 * DmTestCateg filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTestCategFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'created_at'   => new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))),
      'updated_at'   => new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))),
      'position'     => new sfWidgetFormDmFilterInput(),
      'domains_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain')),
    ));

    $this->setValidators(array(
      'created_at'   => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['created_at']->getOption('choices')))),
      'updated_at'   => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['updated_at']->getOption('choices')))),
      'position'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'domains_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain', 'required' => false)),
    ));
    
    $this->mergeI18nForm();


    $this->widgetSchema->setNameFormat('dm_test_categ_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addDomainsListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query->leftJoin('r.DmTestDomainCateg DmTestDomainCateg')
          ->andWhereIn('DmTestDomainCateg.domain_id', $values);
  }

  public function getModelName()
  {
    return 'DmTestCateg';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'created_at'   => 'Date',
      'updated_at'   => 'Date',
      'position'     => 'Number',
      'id'           => 'Number',
      'name'         => 'Text',
      'is_active'    => 'Boolean',
      'lang'         => 'Text',
      'domains_list' => 'ManyKey',
    );
  }
}
