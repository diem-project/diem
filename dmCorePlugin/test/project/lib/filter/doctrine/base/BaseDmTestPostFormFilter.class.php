<?php

/**
 * DmTestPost filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTestPostFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'image_id'    => new sfWidgetFormDoctrineChoice(array('model' => 'DmMedia', 'add_empty' => true)),
      'created_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormInputText(array(), array("class" => "datepicker_me")), 'to_date' => new sfWidgetFormInputText(array(), array("class" => "datepicker_me")), 'with_empty' => false)),
      'updated_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormInputText(array(), array("class" => "datepicker_me")), 'to_date' => new sfWidgetFormInputText(array(), array("class" => "datepicker_me")), 'with_empty' => false)),
      'position'    => new sfWidgetFormFilterInput(),
      'tags_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmTestTag')),
      'medias_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmMedia')),
    ));

    $this->setValidators(array(
      'image_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Image'), 'column' => 'id')),
      'created_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'position'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'tags_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestTag', 'required' => false)),
      'medias_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmMedia', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dm_test_post_filters[%s]');

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

    $query->leftJoin('r.DmTestPostTag DmTestPostTag')
          ->andWhereIn('DmTestPostTag.dm_test_tag_id', $values);
  }

  public function addMediasListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query->leftJoin('r.DmTestPostDmMedia DmTestPostDmMedia')
          ->andWhereIn('DmTestPostDmMedia.dm_media_id', $values);
  }

  public function getModelName()
  {
    return 'DmTestPost';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'image_id'    => 'ForeignKey',
      'created_at'  => 'Date',
      'updated_at'  => 'Date',
      'position'    => 'Number',
      'tags_list'   => 'ManyKey',
      'medias_list' => 'ManyKey',
    );
  }
}
