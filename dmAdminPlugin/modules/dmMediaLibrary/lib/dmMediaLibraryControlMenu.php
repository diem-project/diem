<?php

class dmMediaLibraryControlMenu extends dmMenu
{
  protected
  $folder;

  public function build(DmMediaFolder $folder)
  {
    $this->folder = $folder;
    
    $this->newFolder()->newFile();

    if(!$this->folder->isRoot())
    {
      $this->renameFolder()->moveFolder()->deleteFolder();
    }

    $this->serviceContainer->getService('dispatcher')->notify(
      new sfEvent($this, 'dm.media_library.control_menu', array('folder' => $folder))
    );

    return $this;
  }

  protected function moveFolder()
  {
    return $this->addChild(
      $this->i18n->__('Move this folder'),
      $this->helper->link('dmMediaLibrary/moveFolder?id='.$this->folder->id)
      ->set('.move_folder.dialog_me.s16.s16_folder_move')
    )->end();
  }

  protected function deleteFolder()
  {
    return $this->addChild(
      $this->i18n->__('Delete this folder'),
      $this->helper->link('dmMediaLibrary/deleteFolder?id='.$this->folder->id)
      ->set('.delete_folder.dm_js_confirm.s16.s16_folder_delete')
    )->end();
  }

  protected function renameFolder()
  {
    return $this->addChild(
      $this->i18n->__('Rename this folder'),
      $this->helper->link('dmMediaLibrary/renameFolder?id='.$this->folder->id)
      ->set('.rename_folder.dialog_me.s16.s16_folder_edit')
    )->end();
  }

  protected function newFolder()
  {
    return $this->addChild(
      $this->i18n->__('Add a folder here'),
      $this->helper->link('dmMediaLibrary/newFolder?folder_id='.$this->folder->id)
      ->set('.new_folder.dialog_me.s16.s16_folder_add')
    )->end();
  }

  protected function newFile()
  {
    return $this->addChild(
      $this->i18n->__('Add a file here'),
      $this->helper->link('dmMediaLibrary/saveFile?folder_id='.$this->folder->id)
      ->set('.new_file.dialog_me.s16.s16_file_add.mb20')
    )->end();
  }

}