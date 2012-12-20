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


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmTag', 'column' => 'id')));
		}
		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormDmFilterInput());
			$this->setValidator('name', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}

		if($this->needsWidget('dm_test_fruits_list')){
			$this->setWidget('dm_test_fruits_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'expanded' => true)));
			$this->setValidator('dm_test_fruits_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'required' => false)));
		}
		if($this->needsWidget('dm_test_domains_list')){
			$this->setWidget('dm_test_domains_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain', 'expanded' => true)));
			$this->setValidator('dm_test_domains_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain', 'required' => false)));
		}

		if($this->needsWidget('dm_test_fruit_dm_tag_list')){
			$this->setWidget('dm_test_fruit_dm_tag_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruitDmTag', 'expanded' => true)));
			$this->setValidator('dm_test_fruit_dm_tag_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruitDmTag', 'required' => false)));
		}
		if($this->needsWidget('dm_test_domain_dm_tag_list')){
			$this->setWidget('dm_test_domain_dm_tag_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainDmTag', 'expanded' => true)));
			$this->setValidator('dm_test_domain_dm_tag_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainDmTag', 'required' => false)));
		}


    

    $this->widgetSchema->setNameFormat('dm_tag_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
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

  public function getModelName()
  {
    return 'DmTag';
  }

  public function getFields()
  {
    return array(
      'id'                   => 'Number',
      'name'                 => 'Text',
      'dm_test_fruits_list'  => 'ManyKey',
      'dm_test_domains_list' => 'ManyKey',
    );
  }
}
