<?php

class dmGalleryMediaForm extends DmMediaForm
{
  protected
  $record,
  $fileAlreadyExists;

  public static function factory(myDoctrineRecord $record)
  {
    $form = new self();
    
    $form->setDefault('dm_media_folder_id', $record->getDmMediaFolder()->get('id'));
    
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
  
  protected function setRecord(dmDoctrineRecord $record)
  {
    $this->record = $record;
  }

  protected function throwFileAlreadyExists($validator, $folder, $filename)
  {
    $this->fileAlreadyExists = dmDb::query('DmMedia m')
    ->where('m.dm_media_folder_id = ? AND m.file = ?', array($folder->get('id'), $filename))
    ->fetchRecord();
  }

  public function fileAlreadyExists()
  {
    return $this->fileAlreadyExists;
  }

}