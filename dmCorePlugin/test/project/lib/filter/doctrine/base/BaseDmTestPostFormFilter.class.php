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


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmTestPost', 'column' => 'id')));
		}
		if($this->needsWidget('date')){
			$this->setWidget('date', new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))));
			$this->setValidator('date', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['date']->getOption('choices')))));
		}
		if($this->needsWidget('created_at')){
			$this->setWidget('created_at', new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))));
			$this->setValidator('created_at', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['created_at']->getOption('choices')))));
		}
		if($this->needsWidget('updated_at')){
			$this->setWidget('updated_at', new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))));
			$this->setValidator('updated_at', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['updated_at']->getOption('choices')))));
		}
		if($this->needsWidget('position')){
			$this->setWidget('position', new sfWidgetFormDmFilterInput());
			$this->setValidator('position', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));
		}

		if($this->needsWidget('tags_list')){
			$this->setWidget('tags_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestTag', 'expanded' => true)));
			$this->setValidator('tags_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestTag', 'required' => false)));
		}

		if($this->needsWidget('dm_test_post_tag_list')){
			$this->setWidget('dm_test_post_tag_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPostTag', 'expanded' => true)));
			$this->setValidator('dm_test_post_tag_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPostTag', 'required' => false)));
		}
		if($this->needsWidget('comments_list')){
			$this->setWidget('comments_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestComment', 'expanded' => true)));
			$this->setValidator('comments_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestComment', 'required' => false)));
		}
		if($this->needsWidget('dm_test_post_dm_media_list')){
			$this->setWidget('dm_test_post_dm_media_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPostDmMedia', 'expanded' => true)));
			$this->setValidator('dm_test_post_dm_media_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPostDmMedia', 'required' => false)));
		}

		if($this->needsWidget('categ_list')){
			$this->setWidget('categ_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmTestCateg', 'expanded' => false)));
			$this->setValidator('categ_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmTestCateg', 'required' => true)));
		}
		if($this->needsWidget('author_list')){
			$this->setWidget('author_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'expanded' => false)));
			$this->setValidator('author_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'required' => true)));
		}
		if($this->needsWidget('image_list')){
			$this->setWidget('image_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmMedia', 'expanded' => false)));
			$this->setValidator('image_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmMedia', 'required' => true)));
		}
		if($this->needsWidget('file_list')){
			$this->setWidget('file_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmMedia', 'expanded' => false)));
			$this->setValidator('file_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmMedia', 'required' => true)));
		}
		if($this->needsWidget('created_by_list')){
			$this->setWidget('created_by_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'expanded' => false)));
			$this->setValidator('created_by_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'required' => true)));
		}

    
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
