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
 */
abstract class BaseDmTestPostForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'image_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Image'), 'add_empty' => true)),
      'is_active'   => new sfWidgetFormInputCheckbox(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
      'position'    => new sfWidgetFormInputText(),
        'tags_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmTestTag', 'expanded' => true)),
      ));

    $this->setValidators(array(
      'id'          => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'image_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Image'), 'required' => false)),
      'is_active'   => new sfValidatorBoolean(array('required' => false)),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
      'position'    => new sfValidatorInteger(array('required' => false)),
        'tags_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestTag', 'required' => false)),
      ));

    /*
     * Embed Media form for image_id
     */
    $this->embedForm('image_id_form', $this->createMediaFormForImageId());
    unset($this['image_id']);

    $this->mergeI18nForm();

    $this->widgetSchema->setNameFormat('dm_test_post[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
    
    // Unset automatic fields like 'created_at', 'updated_at', 'position'
    // override this method in your form to keep them
    parent::unsetAutoFields();
  }

  /*
   * Create Media form for image_id
   */
  protected function createMediaFormForImageId()
  {
    return DmMediaForRecordForm::factory($this->object, 'image_id', 'Image', $this->validatorSchema['image_id']->getOption('required'));
  }

  protected function doBind(array $values)
  {
    $values = $this->filterValuesByEmbeddedMediaForm($values, 'image_id');
    parent::doBind($values);
  }
  
  public function processValues($values)
  {
    $values = parent::processValues($values);
    $values = $this->processValuesForEmbeddedMediaForm($values, 'image_id');
    return $values;
  }
  
  protected function doUpdateObject($values)
  {
    parent::doUpdateObject($values);
    $this->doUpdateObjectForEmbeddedMediaForm($values, 'image_id', 'Image');
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
      $this->setDefault('tags_list', $this->object->Tags->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['medias_list']))
    {
      $this->setDefault('medias_list', $this->object->Medias->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveTagsList($con);
    $this->saveMediasList($con);

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

}