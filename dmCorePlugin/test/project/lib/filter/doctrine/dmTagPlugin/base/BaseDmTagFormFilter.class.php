<?php

/**
 * DmTag filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTagFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                 => new sfWidgetFormDmFilterInput(),
      'dm_test_domains_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain')),
      'dm_test_fruits_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit')),
    ));

    $this->setValidators(array(
      'name'                 => new sfValidatorPass(array('required' => false)),
      'dm_test_domains_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain', 'required' => false)),
      'dm_test_fruits_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'required' => false)),
    ));
    

    $this->widgetSchema->setNameFormat('dm_tag_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addDmTestDomainsListColumnQuery(Doctrine_Query $query, $field, $values)
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
          ->andWhereIn('DmTestDomainDmTag.id', $values);
  }

  public function addDmTestFruitsListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query->leftJoin('r.DmTestFruitDmTag DmTestFruitDmTag')
          ->andWhereIn('DmTestFruitDmTag.id', $values);
  }

  public function getModelName()
  {
    return 'DmTag';
  }

  public function getFields()
  {
    return array(
      'id'                   => 'Number',
      'name'                 => 'Text',
      'dm_test_domains_list' => 'ManyKey',
      'dm_test_fruits_list'  => 'ManyKey',
    );
  }
}
