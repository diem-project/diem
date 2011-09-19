<?php

/**
 * DmTestFruit filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTestFruitFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmTestFruit', 'column' => 'id')));
		}
		if($this->needsWidget('title')){
			$this->setWidget('title', new sfWidgetFormDmFilterInput());
			$this->setValidator('title', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}

		if($this->needsWidget('tags_list')){
			$this->setWidget('tags_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTag', 'expanded' => true)));
			$this->setValidator('tags_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTag', 'required' => false)));
		}

		if($this->needsWidget('dm_test_fruit_dm_tag_list')){
			$this->setWidget('dm_test_fruit_dm_tag_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruitDmTag', 'expanded' => true)));
			$this->setValidator('dm_test_fruit_dm_tag_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruitDmTag', 'required' => false)));
		}

		if($this->needsWidget('created_by_list')){
			$this->setWidget('created_by_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'expanded' => false)));
			$this->setValidator('created_by_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'required' => true)));
		}
		if($this->needsWidget('updated_by_list')){
			$this->setWidget('updated_by_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'expanded' => false)));
			$this->setValidator('updated_by_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'required' => true)));
		}

    

    $this->widgetSchema->setNameFormat('dm_test_fruit_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
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

    $query->leftJoin('r.DmTestFruitDmTag DmTestFruitDmTag')
          ->andWhereIn('DmTestFruitDmTag.dm_tag_id', $values);
  }

  public function getModelName()
  {
    return 'DmTestFruit';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'title'      => 'Text',
      'created_by' => 'ForeignKey',
      'updated_by' => 'ForeignKey',
      'tags_list'  => 'ManyKey',
    );
  }
}
