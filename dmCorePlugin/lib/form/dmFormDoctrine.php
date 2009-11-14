<?php

/**
 * Diem form base class.
 *
 * @package    diem
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormBaseTemplate.php 9304 2008-05-27 03:49:32Z dwhittle $
 */
abstract class dmFormDoctrine extends sfFormDoctrine
{
  /*
   * Unset automatic fields like 'created_at', 'updated_at', 'created_by', 'updated_by'
   */
  protected function unsetAutoFields($autoFields = null)
  {
    $autoFields = null === $autoFields ? $this->getAutoFieldsToUnset() : (array) $autoFields;
    
    foreach($autoFields as $autoFieldName)
    {
      if (isset($this->widgetSchema[$autoFieldName]))
      {
        unset($this[$autoFieldName]);
      }
    }
  }
  
  protected function getAutoFieldsToUnset()
  {
    return array('created_at', 'updated_at', 'created_by', 'updated_by', 'rank');
  }

  protected function filterValuesByEmbeddedMediaForm(array $values, $local)
  {
    $formName = $local.'_form';
     
    if (!isset($this->embeddedForms[$formName]))
    {
      return $values;
    }
    
    //no existing media, no file, and it is not required : skip all
    if ($this->embeddedForms[$formName]->getObject()->isNew() && !isset($values[$formName]['file']) && !$this->embeddedForms[$formName]->getValidator('file')->getOption('required'))
    {
      // remove the embedded media form if the file field was not provided
      unset($this->embeddedForms[$formName], $values[$formName]);
      // pass the media form validations
      $this->validatorSchema[$formName] = new sfValidatorPass;
    }

    return $values;
  }

  protected function processValuesForEmbeddedMediaForm(array $values, $local)
  {
    $formName = $local.'_form';

    if (!isset($this->embeddedForms[$formName]))
    {
      return $values;
    }

    // uploading a file
    if($values[$formName]['file'])
    {
      $values[$formName]['dm_media_folder_id'] = $this->object->getTable()->getDmMediaFolder()->get('id');
      /*
       * We have a media with same folder / filename
       * let's use it
       */
      if ($existingMedia = $this->embeddedForms[$formName]->fileAlreadyExists())
      {
        $values[$formName]['id'] = $existingMedia->get('id');
        unset($values[$formName]['file']);
        
        $this->embeddedForms[$formName]->setObject($existingMedia);
      }
      /*
       * We have a new file for an existing media.
       * Let's create a new media
       */
      elseif($values[$formName]['id'])
      {
        $values[$formName]['id'] = null;
        
        $media = new DmMedia;
        $media->Folder = $this->object->getDmMediaFolder();
  
        $this->embeddedForms[$formName]->setObject($media);
      }
    }
    
    return $values;
  }

  protected function doUpdateObjectForEmbeddedMediaForm(array $values, $local, $alias)
  {
    $formName = $local.'_form';

    if (!isset($this->embeddedForms[$formName]))
    {
      return;
    }

    if (!empty($values[$formName]['remove']))
    {
      $this->object->set($alias, null);
    }
    else
    {
      $this->object->set($alias, $this->embeddedForms[$formName]->getObject());
    }
  }


  /**
   * Returns true if the current form has some associated i18n objects.
   *
   * @return Boolean true if the current form has some associated i18n objects, false otherwise
   */
  public function isI18n()
  {
    return $this->getObject()->getTable()->hasI18n();
  }
  
  protected function mergeI18nForm($culture = null)
  {
    $this->mergeForm($this->createI18nForm());
  }
  
  /*
   * Create current i18n form
   */
  protected function createI18nForm($culture = null)
  {
    if (!$this->isI18n())
    {
      throw new dmException(sprintf('The model "%s" is not internationalized.', $this->getModelName()));
    }

    $i18nFormClass = $this->getI18nFormClass();

    $culture = null === $culture ? dmDoctrineRecord::getDefaultCulture() : $culture;

    $i18nForm = new $i18nFormClass($this->object->get('Translation')->get($culture));
    
    unset($i18nForm['id'], $i18nForm['lang']);

    return $i18nForm;
  }

  /**
   * Sets the current object for this form.
   *
   * @return dmDoctrineRecord The current object setted.
   */
  public function setObject(dmDoctrineRecord $record)
  {
    return $this->object = $record;
  }
}