<?php

/**
 * DmTestDomain filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTestDomainFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'created_at'  => new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))),
      'updated_at'  => new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))),
      'position'    => new sfWidgetFormDmFilterInput(),
      'categs_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmTestCateg')),
      'tags_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmTag')),
    ));

    $this->setValidators(array(
      'created_at'  => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['created_at']->getOption('choices')))),
      'updated_at'  => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['updated_at']->getOption('choices')))),
      'position'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'categs_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestCateg', 'required' => false)),
      'tags_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTag', 'required' => false)),
    ));
    
    $this->mergeI18nForm();


    $this->widgetSchema->setNameFormat('dm_test_domain_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addCategsListColumnQuery(Doctrine_Query $query, $field, $values)
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
          ->andWhereIn('DmTestDomainCateg.categ_id', $values);
  }

  public function addTagsListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query->leftJoin('r.DmTestDomainDmTag DmTestDomainDmTag')
          ->andWhereIn('DmTestDomainDmTag.dm_tag_id', $values);
  }

  public function getModelName()
  {
    return 'DmTestDomain';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'created_at'  => 'Date',
      'updated_at'  => 'Date',
      'position'    => 'Number',
      'id'          => 'Number',
      'title'       => 'Text',
      'is_active'   => 'Boolean',
      'lang'        => 'Text',
      'created_by'  => 'ForeignKey',
      'updated_by'  => 'ForeignKey',
      'categs_list' => 'ManyKey',
      'tags_list'   => 'ManyKey',
    );
  }
}
