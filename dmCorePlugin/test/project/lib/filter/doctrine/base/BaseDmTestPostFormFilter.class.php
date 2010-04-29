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
      'categ_id'    => new sfWidgetFormDoctrineChoice(array('model' => 'DmTestCateg', 'add_empty' => true)),
      'user_id'     => new sfWidgetFormDoctrineChoice(array('model' => 'DmUser', 'add_empty' => true)),
      'image_id'    => new sfWidgetFormDoctrineChoice(array('model' => 'DmMedia', 'add_empty' => true)),
      'file_id'     => new sfWidgetFormDoctrineChoice(array('model' => 'DmMedia', 'add_empty' => true)),
      'date'        => new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))),
      'created_by'  => new sfWidgetFormDoctrineChoice(array('model' => 'DmUser', 'add_empty' => true)),
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
      'tags_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmTestTag')),
      'medias_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmMedia')),
    ));

    $this->setValidators(array(
      'categ_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Categ'), 'column' => 'id')),
      'user_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Author'), 'column' => 'id')),
      'image_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Image'), 'column' => 'id')),
      'file_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('File'), 'column' => 'id')),
      'date'        => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['date']->getOption('choices')))),
      'created_by'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('CreatedBy'), 'column' => 'id')),
      'created_at'  => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['created_at']->getOption('choices')))),
      'updated_at'  => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['updated_at']->getOption('choices')))),
      'position'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'tags_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestTag', 'required' => false)),
      'medias_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmMedia', 'required' => false)),
    ));
    
    $this->mergeI18nForm();


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
          ->andWhereIn('DmTestPostTag.tag_id', $values);
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
      'categ_id'    => 'ForeignKey',
      'user_id'     => 'ForeignKey',
      'image_id'    => 'ForeignKey',
      'file_id'     => 'ForeignKey',
      'date'        => 'Date',
      'created_by'  => 'ForeignKey',
      'created_at'  => 'Date',
      'updated_at'  => 'Date',
      'position'    => 'Number',
      'id'          => 'Number',
      'title'       => 'Text',
      'excerpt'     => 'Text',
      'body'        => 'Text',
      'url'         => 'Text',
      'is_active'   => 'Boolean',
      'lang'        => 'Text',
      'version'     => 'Number',
      'tags_list'   => 'ManyKey',
      'medias_list' => 'ManyKey',
    );
  }
}
