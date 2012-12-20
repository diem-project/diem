<?php

/**
 * DmTestPost form base class.
 *
 * @method DmTestPost getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmTestPostForm extends BaseFormDoctrine
{
  public function setup()
  {
    parent::setup();

		//column
		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormInputHidden());
			$this->setValidator('id', new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)));
		}
		//column
		if($this->needsWidget('date')){
			$this->setWidget('date', new sfWidgetFormDmDate());
			$this->setValidator('date', new dmValidatorDate());
		}
		//column
		if($this->needsWidget('created_at')){
			$this->setWidget('created_at', new sfWidgetFormDateTime());
			$this->setValidator('created_at', new sfValidatorDateTime());
		}
		//column
		if($this->needsWidget('updated_at')){
			$this->setWidget('updated_at', new sfWidgetFormDateTime());
			$this->setValidator('updated_at', new sfValidatorDateTime());
		}
		//column
		if($this->needsWidget('position')){
			$this->setWidget('position', new sfWidgetFormInputText());
			$this->setValidator('position', new sfValidatorInteger(array('required' => false)));
		}

		//many to many
		if($this->needsWidget('tags_list')){
			$this->setWidget('tags_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestTag', 'expanded' => true)));
			$this->setValidator('tags_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestTag', 'required' => false)));
		}

		//one to many
		if($this->needsWidget('dm_test_post_tag_list')){
			$this->setWidget('dm_test_post_tag_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPostTag', 'expanded' => true)));
			$this->setValidator('dm_test_post_tag_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPostTag', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('comments_list')){
			$this->setWidget('comments_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestComment', 'expanded' => true)));
			$this->setValidator('comments_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestComment', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_test_post_dm_media_list')){
			$this->setWidget('dm_test_post_dm_media_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPostDmMedia', 'expanded' => true)));
			$this->setValidator('dm_test_post_dm_media_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPostDmMedia', 'required' => false)));
		}

		//one to one
		if($this->needsWidget('categ_id')){
			$this->setWidget('categ_id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmTestCateg', 'expanded' => false)));
			$this->setValidator('categ_id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmTestCateg', 'required' => true)));
		}
		//one to one
		if($this->needsWidget('user_id')){
			$this->setWidget('user_id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'expanded' => false)));
			$this->setValidator('user_id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'required' => true)));
		}
		//one to one
		if($this->needsWidget('image_id')){
			$this->setWidget('image_id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmMedia', 'expanded' => false)));
			$this->setValidator('image_id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmMedia', 'required' => false)));
		}
		//one to one
		if($this->needsWidget('file_id')){
			$this->setWidget('file_id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmMedia', 'expanded' => false)));
			$this->setValidator('file_id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmMedia', 'required' => false)));
		}
		//one to one
		if($this->needsWidget('created_by')){
			$this->setWidget('created_by', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'expanded' => false)));
			$this->setValidator('created_by', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'required' => false)));
		}



    /*
     * Embed Media form for image_id
     */
    if($this->needsWidget('image_id')){
      $this->embedForm('image_id_form', $this->createMediaFormForImageId());
      unset($this['image_id']);
    }
    /*
     * Embed Media form for file_id
     */
    if($this->needsWidget('file_id')){
      $this->embedForm('file_id_form', $this->createMediaFormForFileId());
      unset($this['file_id']);
    }

    if('embed' == sfConfig::get('dm_i18n_form'))
    {
      $this->embedI18n(sfConfig::get('dm_i18n_cultures'));
    }
    else
    {
      $this->mergeI18nForm();
    }

    $this->widgetSchema->setNameFormat('dm_test_post[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
    
    // Unset automatic fields like 'created_at', 'updated_at', 'position'
    // override this method in your form to keep them
    parent::unsetAutoFields();
  }

  /**
   * Creates a DmMediaForm instance for image_id
   *
   * @return DmMediaForm a form instance for the related media
   */
  protected function createMediaFormForImageId()
  {
    return DmMediaForRecordForm::factory($this->object, 'image_id', 'Image', $this->validatorSchema['image_id']->getOption('required'));
  }
  /**
   * Creates a DmMediaForm instance for file_id
   *
   * @return DmMediaForm a form instance for the related media
   */
  protected function createMediaFormForFileId()
  {
    return DmMediaForRecordForm::factory($this->object, 'file_id', 'File', $this->validatorSchema['file_id']->getOption('required'));
  }

  protected function doBind(array $values)
  {
    $values = $this->filterValuesByEmbeddedMediaForm($values, 'image_id');
    $values = $this->filterValuesByEmbeddedMediaForm($values, 'file_id');
    parent::doBind($values);
  }
  
  public function processValues($values)
  {
    $values = parent::processValues($values);
    $values = $this->processValuesForEmbeddedMediaForm($values, 'image_id');
    $values = $this->processValuesForEmbeddedMediaForm($values, 'file_id');
    return $values;
  }
  
  protected function doUpdateObject($values)
  {
    parent::doUpdateObject($values);
    $this->doUpdateObjectForEmbeddedMediaForm($values, 'image_id', 'Image');
    $this->doUpdateObjectForEmbeddedMediaForm($values, 'file_id', 'File');
  }

  public function getModelName()
  {
    return 'DmTestPost';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['tags_list']))
    {
        $this->setDefault('tags_list', array_merge((array)$this->getDefault('tags_list'),$this->object->Tags->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['medias_list']))
    {
        $this->setDefault('medias_list', array_merge((array)$this->getDefault('medias_list'),$this->object->Medias->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_test_post_tag_list']))
    {
        $this->setDefault('dm_test_post_tag_list', array_merge((array)$this->getDefault('dm_test_post_tag_list'),$this->object->DmTestPostTag->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['comments_list']))
    {
        $this->setDefault('comments_list', array_merge((array)$this->getDefault('comments_list'),$this->object->Comments->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['translation_list']))
    {
        $this->setDefault('translation_list', array_merge((array)$this->getDefault('translation_list'),$this->object->Translation->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_test_post_dm_media_list']))
    {
        $this->setDefault('dm_test_post_dm_media_list', array_merge((array)$this->getDefault('dm_test_post_dm_media_list'),$this->object->DmTestPostDmMedia->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveTagsList($con);
    $this->saveMediasList($con);
    $this->saveDmTestPostTagList($con);
    $this->saveCommentsList($con);
    $this->saveTranslationList($con);
    $this->saveDmTestPostDmMediaList($con);

    parent::doSave($con);
  }

  public function saveTagsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['tags_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Tags->getPrimaryKeys();
    $values = $this->getValue('tags_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Tags', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Tags', array_values($link));
    }
  }

  public function saveMediasList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['medias_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Medias->getPrimaryKeys();
    $values = $this->getValue('medias_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Medias', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Medias', array_values($link));
    }
  }

  public function saveDmTestPostTagList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_test_post_tag_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmTestPostTag->getPrimaryKeys();
    $values = $this->getValue('dm_test_post_tag_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmTestPostTag', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmTestPostTag', array_values($link));
    }
  }

  public function saveCommentsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['comments_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Comments->getPrimaryKeys();
    $values = $this->getValue('comments_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Comments', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Comments', array_values($link));
    }
  }

  public function saveTranslationList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['translation_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Translation->getPrimaryKeys();
    $values = $this->getValue('translation_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Translation', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Translation', array_values($link));
    }
  }

  public function saveDmTestPostDmMediaList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_test_post_dm_media_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmTestPostDmMedia->getPrimaryKeys();
    $values = $this->getValue('dm_test_post_dm_media_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmTestPostDmMedia', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmTestPostDmMedia', array_values($link));
    }
  }

}
