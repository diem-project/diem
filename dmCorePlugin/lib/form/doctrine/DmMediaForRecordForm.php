<?php

class DmMediaForRecordForm extends DmMediaForm
{
  protected
  $fileAlreadyExists;

  public static function factory(myDoctrineRecord $record, $local, $alias, $required)
  {
    /*
     * Check first is local column has a value
     * not to modify the record
     */
    if ($record->get($local))
    {
      $media = $record->get($alias);
    }
    else
    {
      $media = new DmMedia;
      $media->set('Folder', $record->getDmMediaFolder());
    }

    $form = new self($media);
    $form->configureRequired($required);
    return $form;
  }

  public function resetFormFields()
  {
    parent::resetFormFields();
    $this->fileAlreadyExists = null;
  }

  public function configureRequired($required)
  {
    $this->getValidator('file')->setOption('required', $required && $this->getValidator('file')->getOption('required'));

    /*
     * Add checkbox to remove Media
     */
    if(!$required && $this->object->exists() && !isset($this->widgetSchema['remove']))
    {
      $this->widgetSchema['remove'] = new sfWidgetFormInputCheckbox;
      $this->validatorSchema['remove'] = new sfValidatorBoolean;
    }
    elseif(isset($this->widgetSchema['remove']))
    {
      unset($this->widgetSchema['remove'], $this->validatorSchema['remove']);
    }
  }

  protected function throwFileAlreadyExists($validator, $folder, $filename)
  {
    $this->fileAlreadyExists = dmDb::query('DmMedia m')
    ->where('m.dm_media_folder_id = ? AND m.file = ?', array($folder->id, $filename))
    ->fetchRecord();
  }

  public function fileAlreadyExists()
  {
    return $this->fileAlreadyExists;
  }

}