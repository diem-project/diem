<?php

class DmAdminMediaForm extends DmMediaForm
{
  public function configure()
  {
    parent::configure();

    if($this->object->exists())
    {
      $folderChoices = $this->getFolderChoices();

      unset($this['dm_media_folder_id']);
      
      $this->widgetSchema['dm_media_folder_id'] = new sfWidgetFormChoice(array(
        'choices' => $folderChoices
      ));
      $this->validatorSchema['dm_media_folder_id'] = new sfValidatorChoice(array(
        'choices' => array_keys($folderChoices),
        'required' => true
      ));
      $this->widgetSchema->setLabel('dm_media_folder_id', 'Move to');
    }
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    foreach ($taintedFiles as $key => $data) {
      $filename = $data['name'];

      $taintedFiles[$key]['name'] = dmOs::getFileWithoutExtension($filename) .
                                    strtolower(dmOs::getFileExtension($filename));
    }

    parent::bind($taintedValues, $taintedFiles);
  }

  protected function getFolderChoices()
  {
    $_folderChoices = dmDb::query('DmMediaFolder f')
    ->orderBy('f.lft')
    ->select('f.id, f.level, f.rel_path')
    ->fetchPDO();

    $folderChoices = array();
    foreach($_folderChoices as $values)
    {
      $name = basename($values[2]) ? basename($values[2]) : 'root';
      $folderChoices[$values[0]] = str_repeat('&nbsp;&nbsp;', $values[1]).'-&nbsp;'.$name;
    }

    return $folderChoices;
  }
}