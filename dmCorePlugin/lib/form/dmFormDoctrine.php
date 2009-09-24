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
  protected
  $autoFieldsToUnset = array('created_at', 'updated_at', 'created_by', 'updated_by');
  
  /*
   * Unset automatic fields like 'created_at', 'updated_at', 'created_by', 'updated_by'
   */
  protected function unsetAutoFields($autoFields = null)
  {
    $autoFields = is_array($autoFields) ? $autoFields : $this->autoFieldsToUnset;
    
    foreach($autoFields as $autoFieldName)
    {
      if (isset($this->widgetSchema[$autoFieldName]))
      {
        unset($this[$autoFieldName]);
      }
    }
  }

  protected function filterValuesByEmbeddedMediaForm(array $values, $local)
  {
    $formName = $local.'_form';
     
    //no existing media, no file, and it is not required : skip all
    if ($this->embeddedForms[$formName]->getObject()->isNew() && !$values[$formName]['file']['size'] && !$this->embeddedForms[$formName]->getValidator('file')->getOption('required'))
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
      /*
       * We have a media with same folder / filename
       * let's use it
       */
      if ($existingMedia = $this->embeddedForms[$formName]->fileAlreadyExists())
      {
        $values[$formName]['id'] = $existingMedia->id;
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


  /*
   * Create current i18n form
   */
  protected function createCurrentI18nForm()
  {
    if (!$this->isI18n())
    {
      throw new dmException(sprintf('The model "%s" is not internationalized.', $this->getModelName()));
    }

    $i18nFormClass = $this->getI18nFormClass();

    $culture = dm::getUser()->getCulture();

    $i18nObject = $this->object->Translation[$culture];
    $i18nForm = new $i18nFormClass($i18nObject);
    unset($i18nForm['id'], $i18nForm['lang']);

    return $i18nForm;
  }

  /**
   * Sets the current object for this form.
   *
   * @return BaseObject The current object setted.
   */
  public function setObject(myDoctrineRecord $record)
  {
    return $this->object = $record;
  }
}