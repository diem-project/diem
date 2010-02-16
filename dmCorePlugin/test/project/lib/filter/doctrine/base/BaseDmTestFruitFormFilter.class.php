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
    $this->setWidgets(array(
      'title'     => new sfWidgetFormFilterInput(),
      'tags_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmTag')),
    ));

    $this->setValidators(array(
      'title'     => new sfValidatorPass(array('required' => false)),
      'tags_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTag', 'required' => false)),
    ));

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
      'id'        => 'Number',
      'title'     => 'Text',
      'tags_list' => 'ManyKey',
    );
  }
}
