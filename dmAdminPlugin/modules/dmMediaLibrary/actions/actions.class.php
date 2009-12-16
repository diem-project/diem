<?php

class dmMediaLibraryActions extends dmAdminBaseActions
{

  public function executeFile(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->file = dmDb::table('DmMedia')->find($request->getParameter('media_id')),
      'media not found'
    );

    if (!$this->file->isWritable())
    {
      $this->getUser()->logAlert($this->context->getI18n()->__('This file is not writable'), false);
    }

    $this->form = new DmMediaForm($this->file);
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->redirect('@dm_media_library_path');
  }

  public function executePath(sfWebRequest $request)
  {
    $this->context->getEventDispatcher()->connect('dm.bread_crumb.filter_links', array($this, 'listenToBreadCrumbFilterLinksEvent'));
    
    $path = $request->getParameter('path', '');

    if(!$this->folder = dmDb::table('DmMediaFolder')->findOneByRelPath($path))
    {
      $this->getUser()->logError(sprintf('Media folder %s does not exist', $path));

      dmDb::table('DmMediaFolder')->checkRoot();

      $this->redirect('@dm_media_library_path');
    }

    if (!$this->folder->isWritable())
    {
      $this->getUser()->logAlert($this->context->getI18n()->__('This folder is not writable'), false);
    }

    $this->folder->sync();

    $this->files = $this->folder->Medias;

    $this->metadata = array();
    if($this->getUser()->hasFlash('dm_media_open'))
    {
      $this->metadata['open_media'] = $this->getUser()->getFlash('dm_media_open');
    }
  }
  
  public function listenToBreadCrumbFilterLinksEvent(sfEvent $event, array $links)
  {
    unset($links['action']);
    
    if ($ancestors = $this->folder->getNode()->getAncestors())
    {
      foreach($ancestors as $parent)
      {
        $links[] = $this->context->getHelper()->£link($this->getRouting()->getMediaUrl($parent))->text($parent->get('name'));
      }
    }
    
    $links[] = $this->context->getHelper()->£('h1', $this->folder->get('name'));
    
    return $links;
  }

  public function executeNewFile(sfWebRequest $request)
  {
    $this->forward404Unless(
      $parent = dmDb::table('DmMediaFolder')->find($request->getParameter('folder_id')),
      sprintf('There is no parent %d', $request->getParameter('folder_id'))
    );

    if  (!$parent->isWritable())
    {
      $this->getUser()->logAlert(
        $this->context->getI18n()->__('Folder %1% is not writable', array('%1%' => $parent->getFullPath()))
      );

      return $this->renderPartial('dmInterface/flash');
    }

    $this->form = new DmMediaForm();
    $this->form->setDefault('dm_media_folder_id', $parent->getId());

    $this->setTemplate('editFile');
  }

  public function executeSaveFile(sfWebRequest $request)
  {
    if ($mediaId = dmArray::get($request->getParameter('dm_media_form'), 'id'))
    {
      $this->forward404Unless($media = dmDb::table('DmMedia')->find($mediaId));
    }
    else
    {
      $media = null;
    }
    
    $this->form = new DmMediaForm($media);

    if ($this->form->bindAndValid($request))
    {
      $object = $this->form->save();

      if($this->form->getValue('file'))
      {
        $this->getUser()->setFlash('dm_media_open', $object->id, false);
        return $this->renderText('[OK]|'.$this->getRouting()->getMediaUrl($object->Folder));
      }
    }

    $this->setTemplate('editFile');
  }

  public function executeDeleteFile(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->file = dmDb::table('DmMedia')->find($request->getParameter('media_id')),
      'can not find media'
    );

    if (!$this->file->isWritable())
    {
      $this->getUser()->logAlert($this->context->getI18n()->__('File %1% is not writable', array('%1%' => $this->file->getRelPath())));
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
      $this->folder = dmDb::table('DmMediaFolder')->find($request->getParameter('folder_id')),
      'can not find folder'
    );

    if(!$parent = $this->folder->getNode()->getParent())
    {
      throw new dmException(sprintf('Can not rename folder %s wich has no parent', $this->folder));
    }

    if (!$this->folder->isWritable())
    {
      $this->getUser()->logAlert($this->context->getI18n()->__('Folder %1% is not writable', array('%1%' => $this->folder->getRelPath())));
      return $this->renderPartial('dmInterface/flash');
    }

    $this->form = new DmMediaFolderForm($this->folder);
    $this->form->setDefault('parent_id', $parent->getId());
    $this->form->setDefault('id', $this->folder->getId());

    $this->setTemplate('editFolder');
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
        $this->context->getI18n()->__('Folder %1% is not writable', array('%1%' => $parent->getFullPath())),
        false
      );

      return $this->renderPartial('dmInterface/flash');
    }

    $this->form = new DmAdminNewMediaFolderForm;
    $this->form->setDefault('parent_id', $parent->id);
  }
  
  public function executeCreateFolder(dmWebRequest $request)
  {
    $this->form = new DmAdminNewMediaFolderForm;
    
    if ($this->form->bindAndValid($request))
    {
      $this->form->save();

      return $this->renderText('[OK]|'.$this->getRouting()->getMediaUrl($this->form->getObject()));
    }
    
    $this->setTemplate('newFolder');
  }

  public function executeSaveFolder(sfWebRequest $request)
  {
    $this->form = new DmAdminMediaFolderForm(
      dmDb::table('DmMediaFolder')->find(dmArray::get($request->getParameter('dm_media_folder'), 'id')),
      'can not find folder'
    );

    if ($this->form->bindAndValid($request))
    {
      $oldName = $this->form->getObject()->getName();

      $object = $this->form->updateObject();

      $this->forward404Unless(
        $parent = dmDb::table('DmMediaFolder')->find($this->form->getValue('parent_id')),
        sprintf('There is no parent %d', $this->form->getValue('parent_id'))
      );

      if ($object->isNew())
      {
        $object->getNode()->insertAsLastChildOf($parent);
      }
      else
      {
        $object->setName($oldName);
        $object->rename($this->form->getValue('name'));
      }

      $object->save();

      return $this->renderText('[OK]|'.$this->getRouting()->getMediaUrl($object));
    }

    $this->setTemplate('editFolder');
  }

  public function executeDeleteFolder(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->folder = dmDb::table('DmMediaFolder')->find($request->getParameter('folder_id'))
    );

    if(!$parent = $this->folder->getNode()->getParent())
    {
      throw new dmException(sprintf('Can not delete folder %s wich has no parent', $this->folder));
    }

    if (!$this->folder->isWritable())
    {
      $this->getUser()->logAlert($this->context->getI18n()->__('Folder %1% is not writable', array('%1%' => $this->folder->getRelPath())));
    }
    else
    {
      $this->folder->delete();
    }

    return $this->redirect($this->getRouting()->getMediaUrl($parent));
  }

}