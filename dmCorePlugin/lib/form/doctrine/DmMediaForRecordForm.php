<?php

class DmMediaForRecordForm extends DmMediaForm
{
  protected
  $record,
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

    $form = new self($media);
    $form->configureRequired($required);
    $form->setRecord($record);
    return $form;
  }

  public function resetFormFields()
  {
    parent::resetFormFields();
    $this->fileAlreadyExists = null;
  }
  
  public function checkExistingNameInParent($validator, $values)
  {
    if (!empty($values['file']))
    {
      $values['dm_media_folder_id'] = $this->record->getDmMediaFolder()->get('id');
    }
    
    return parent::checkExistingNameInParent($validator, $values);
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
  
  protected function setRecord(dmDoctrineRecord $record)
  {
    $this->record = $record;
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