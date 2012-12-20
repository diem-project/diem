<?php

/**
 * DmMedia form base class.
 *
 * @method DmMedia getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDmMediaForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'dm_media_folder_id' => new sfWidgetFormDmDoctrineChoice(array('model' => $this->getRelatedModelName('Folder'), 'add_empty' => false)),
      'file'               => new sfWidgetFormInputText(),
      'mime'               => new sfWidgetFormInputText(),
      'size'               => new sfWidgetFormInputText(),
      'dimensions'         => new sfWidgetFormInputText(),
      'created_at'         => new sfWidgetFormDateTime(),
      'updated_at'         => new sfWidgetFormDateTime(),
      'dm_test_post_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost')),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'dm_media_folder_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Folder'))),
      'file'               => new sfValidatorString(array('max_length' => 255)),
      'mime'               => new sfValidatorString(array('max_length' => 128)),
      'size'               => new sfValidatorInteger(array('required' => false)),
      'dimensions'         => new sfValidatorString(array('max_length' => 15, 'required' => false)),
      'created_at'         => new sfValidatorDateTime(),
      'updated_at'         => new sfValidatorDateTime(),
      'dm_test_post_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmMedia', 'column' => array('dm_media_folder_id', 'file')))
    );

    $this->widgetSchema->setNameFormat('dm_media[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmMedia';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['dm_test_post_list']))
    {
      $this->setDefault('dm_test_post_list', $this->object->DmTestPost->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveDmTestPostList($con);

    parent::doSave($con);
  }

  public function saveDmTestPostList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_test_post_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmTestPost->getPrimaryKeys();
    $values = $this->getValue('dm_test_post_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmTestPost', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmTestPost', array_values($link));
    }
  }

}
