<?php

class BasedmMediaLibraryActions extends dmAdminBaseActions
{

  public function executeFile(sfWebRequest $request)
  {
    $this->setLayout(false);

    $this->forward404Unless(
      $this->file = dmDb::table('DmMedia')->find($request->getParameter('media_id')),
      'media not found'
    );

    if (!$this->file->isWritable())
    {
      $this->getUser()->logAlert($this->getI18n()->__('This file is not writable'), false);
    }

    $this->form = new DmAdminMediaForm($this->file);
  }

  public function executeIndex(sfWebRequest $request)
  {
    return $this->redirect('@dm_media_library_path');
  }

  public function executePath(sfWebRequest $request)
  {
    $this->context->getEventDispatcher()->connect('dm.bread_crumb.filter_links', array($this, 'listenToBreadCrumbFilterLinksEvent'));

    $path = $request->getParameter('path', '');

    if(!$this->folder = dmDb::table('DmMediaFolder')->findOneByRelPath($path))
    {
      $this->getUser()->logError(sprintf('Media folder %s does not exist', $path));

      dmDb::table('DmMediaFolder')->checkRoot();

      return $this->redirect('@dm_media_library_path');
    }

    if (!$this->folder->isWritable())
    {
      $this->getUser()->logAlert($this->getI18n()->__('This folder is not writable'), false);
    }

    $this->folder->sync();

    $this->files = $this->folder->Medias;

    $this->metadata = array();
    if($this->getUser()->hasFlash('dm_media_open'))
    {
      $this->metadata['open_media'] = $this->getUser()->getFlash('dm_media_open');
    }

    $this->controlMenu = $this->getService('menu', 'dmMediaLibraryControlMenu')->build($this->folder);
  }

  public function listenToBreadCrumbFilterLinksEvent(sfEvent $event, array $links)
  {
    unset($links['action']);

    if ($ancestors = $this->folder->getNode()->getAncestors())
    {
      foreach($ancestors as $parent)
      {
        $links[] = $this->getHelper()->link($this->getRouting()->getMediaUrl($parent))->text($parent->get('name'));
      }
    }

    $links[] = $this->getHelper()->tag('h1', $this->folder->get('name'));

    return $links;
  }

  public function executeSaveFile(dmWebRequest $request)
  {
    // modify existing media
    if ($mediaId = dmArray::get($request->getParameter('dm_admin_media_form'), 'id'))
    {
      $this->forward404Unless($media = dmDb::table('DmMedia')->find($mediaId));
      $form = new DmAdminMediaForm($media);
    }
    // create new media
    else
    {
      $media = null;

      $this->forward404Unless($folder = dmDb::table('DmMediaFolder')->find($request->getParameter('folder_id')));

      if(!$folder->isWritable())
      {
        $this->getUser()->logAlert($this->getI18n()->__('Folder %1% is not writable', array('%1%' => $folder->fullPath)));
      }
      
      $form = new DmAdminMediaForm();
      $form->setDefault('dm_media_folder_id', $folder->id);
    }
    
    if ($request->isMethod('post') && $form->bindAndValid($request))
    {
      $redirect = $form->getValue('file') || $media->dm_media_folder_id != $form->getValue('dm_media_folder_id');

      $media = $form->save();

      if($redirect)
      {
        $this->getUser()->setFlash('dm_media_open', $media->id, false);
        return $this->renderText($this->getRouting()->getMediaUrl(dmDb::table('DmMediaFolder')->find($media->dm_media_folder_id)));
      }
    }

    $action = $media ? 'dmMediaLibrary/saveFile' : 'dmMediaLibrary/saveFile?folder_id='.$folder->id;
    return $this->renderText($form->render('.dm_form.list.little action="'.$action.'"'));
  }

  public function executeDeleteFile(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->file = dmDb::table('DmMedia')->find($request->getParameter('media_id')),
      'can not find media'
    );

    if (!$this->file->isWritable())
    {
      $this->getUser()->logAlert($this->getI18n()->__('File %1% is not writable', array('%1%' => $this->file->getRelPath())));
    }
    else
    {
      $this->file->delete();
    }

    return $this->redirect($this->getRouting()->getMediaUrl($this->file->Folder));
  }

  public function executeRenameFolder(sfWebRequest $request)
  {
    $this->forward404Unless(
      $folder = dmDb::table('DmMediaFolder')->find($request->getParameter('id')),
      'can not find folder'
    );

    if (!$folder->isWritable())
    {
      $this->getUser()->logAlert($this->getI18n()->__('Folder %1% is not writable', array('%1%' => $folder->getRelPath())));
      return $this->renderPartial('dmInterface/flash');
    }

    $form = new DmAdminRenameMediaFolderForm($folder);

    if ($request->isMethod('post') && $form->bindAndValid($request))
    {
      return $this->renderText($this->getRouting()->getMediaUrl($form->save()));
    }

    return $this->renderText($form->render('.dm_form.list.little action="dmMediaLibrary/renameFolder?id='.$folder->id.'"'));
  }

  public function executeMoveFolder(sfWebRequest $request)
  {
    $this->forward404Unless(
      $folder = dmDb::table('DmMediaFolder')->find($request->getParameter('id')),
      'can not find folder'
    );

    if (!$folder->isWritable())
    {
      $this->getUser()->logAlert($this->getI18n()->__('Folder %1% is not writable', array('%1%' => $folder->getRelPath())));
      return $this->renderPartial('dmInterface/flash');
    }

    $form = new DmAdminMoveMediaFolderForm($folder);

    if ($request->isMethod('post') && $form->bindAndValid($request))
    {
      return $this->renderText($this->getRouting()->getMediaUrl($form->save()));
    }

    return $this->renderText($form->render('.dm_form.list.little action="dmMediaLibrary/moveFolder?id='.$folder->id.'"'));
  }

  public function executeNewFolder(sfWebRequest $request)
  {
    $this->forward404Unless(
      $parent = dmDb::table('DmMediaFolder')->find($request->getParameter('folder_id')),
      sprintf('There is no parent %d', $request->getParameter('folder_id'))
    );

    if  (!$parent->isWritable())
    {
      $this->getUser()->logAlert(
        $this->getI18n()->__('Folder %1% is not writable', array('%1%' => $parent->getFullPath())),
        false
      );

      return $this->renderPartial('dmInterface/flash');
    }

    $form = new DmAdminNewMediaFolderForm;
    $form->setDefault('parent_id', $parent->id);

    return $this->renderText($form->render('.dm_form.list.little action=dmMediaLibrary/createFolder'));
  }

  public function executeCreateFolder(dmWebRequest $request)
  {
    $form = new DmAdminNewMediaFolderForm;

    if ($form->bindAndValid($request))
    {
      return $this->renderText($this->getRouting()->getMediaUrl($form->save()));
    }

    return $this->renderText($form->render('.dm_form.list.little action=dmMediaLibrary/createFolder'));
  }

  public function executeDeleteFolder(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->folder = dmDb::table('DmMediaFolder')->find($request->getParameter('id'))
    );

    if(!$parent = $this->folder->getNode()->getParent())
    {
      throw new dmException(sprintf('Can not delete folder %s wich has no parent', $this->folder));
    }

    if (!$this->folder->isWritable())
    {
      $this->getUser()->logAlert($this->getI18n()->__('Folder %1% is not writable', array('%1%' => $this->folder->getRelPath())));
    }
    else
    {
      $this->folder->delete();
    }

    return $this->redirect($this->getRouting()->getMediaUrl($parent));
  }

}