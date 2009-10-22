<?php

class dmMediaLibraryActions extends dmAdminBaseActions
{

  public function executeEditImage(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->file = dmDb::table('DmMedia')->find($request->getParameter('media_id')),
      'media not found'
    );

    $this->file->backup();

    $this->absoluteWebUrl = $this->file->getFullWebPath();
    $this->absoluteServerUrl = $this->file->getFullPath();

    $this->target = $this->generateUrl('dm_media_library', array(
      'action'    => 'editImageSave',
      'media_id'  => $this->file->getId()
    ), true);

    $this->options = array(
      'loc'       => $this->getUser()->getCulture(),
      'save_to'   => $this->file->Folder->getRelPath(),
      'referrer'  => dmConfig::get('site_name'),
      'skip_default' => true,
      'exit'      => $this->generateUrl('dm_media_library_path', array(
        'path'    => $this->file->Folder->getRelPath()
      ), true)
    );

    sfConfig::set('dm_admin_full_screen', true);
  }

  public function executeEditImageSave(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->file = dmDb::table('DmMedia')->find($request->getParameter('media_id')),
      'meddia not found'
    );

    $this->file->destroyThumbnails();

    $dmPixlr = new dmPixlr();
    $dmPixlr->save($request);

    $this->redirect($this->generateUrl('dm_media_library_path', array(
      'path'    => $this->file->Folder->getRelPath()
    )));
  }

  public function executeFile(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->file = dmDb::table('DmMedia')->find($request->getParameter('media_id')),
      'media not found'
    );

    if (!$this->file->isWritable())
    {
      $this->getUser()->logAlert(dm::getI18n()->__('This file is not writable'));
    }

    $this->form = new DmMediaForm($this->file);
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->redirect('@dm_media_library_path');
  }

  public function executePath(sfWebRequest $request)
  {
    $this->context->getEventDispatcher()->connect('dm.bread_crumb.filterLinks', array($this, 'listenToBreadCrumbFilterLinksEvent'));
    
    $path = $request->getParameter('path', '');

    if(!$this->folder = dmDb::table('DmMediaFolder')->findOneByRelPath($path))
    {
      $this->getUser()->logError(sprintf('Media folder %s does not exist', $path));

      dmDb::table('DmMediaFolder')->checkRoot();

      $this->redirect('@dm_media_library_path');
    }

    if (!$this->folder->isWritable())
    {
      $this->getUser()->logAlert(dm::getI18n()->__('This folder is not writable'), false);
    }

    $this->folder->sync();

    $this->files = $this->folder->Medias;

    $this->metadata = array();
    if($this->getUser()->hasFlash('dm_media_open'))
    {
      $this->metadata['open_media'] = $this->getUser()->getFlash('dm_media_open');
    }
  }
  
  public function listenToBreadCrumbFilterLinksEvent(sfEvent $event, $links)
  {
    unset($links['action']);
    
    if ($ancestors = $this->folder->getNode()->getAncestors())
    {
      foreach($ancestors as $parent)
      {
        $links[] = dmLinkTag::build(dmMediaTools::getAdminUrlFor($parent))->text($parent->get('name'));
      }
    }
    
    $links[] = £('h1', $this->folder->get('name'));
    
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
        dm::getI18n()->__('Folder %1% is not writable', array('%1%' => $parent->getFullPath()))
      );

      return $this->renderPartial('dmAdmin/flash');
    }

    $this->form = new DmMediaForm();
    $this->form->setDefault('dm_media_folder_id', $parent->getId());

    $this->setTemplate('editFile');
  }

  public function executeSaveFile(sfWebRequest $request)
  {
    $this->form = new DmMediaForm(
      dmDb::table('DmMedia')->find(dmArray::get($request->getParameter('dm_media'), 'id'))
    );

    if ($this->form->bindAndValid($request))
    {
      if($object = $this->form->updateObject())
      {
        $object->save();

        if($this->form->getValue('file'))
        {
          $this->getUser()->setFlash('dm_media_open', $object->getId());
          return $this->renderText('[OK]|'.dmMediaTools::getAdminUrlFor($object->Folder));
        }
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
      $this->getUser()->logAlert(dm::getI18n()->__('File %1% is not writable', array('%1%' => $this->file->getRelPath())));
    }
    else
    {
      $this->file->delete();
    }

    return $this->redirect(dmMediaTools::getAdminUrlFor($this->file->Folder));

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
      $this->getUser()->logAlert(dm::getI18n()->__('Folder %1% is not writable', array('%1%' => $this->folder->getRelPath())));
      return $this->renderPartial('dmAdmin/flash');
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
        dm::getI18n()->__('Folder %1% is not writable', array('%1%' => $parent->getFullPath()))
      );

      return $this->renderPartial('dmAdmin/flash');
    }

    $this->form = new DmMediaFolderForm();
    $this->form->setDefault('parent_id', $parent->getId());

    $this->setTemplate('editFolder');
  }

  public function executeSaveFolder(sfWebRequest $request)
  {
    $this->form = new DmMediaFolderForm(
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

      return $this->renderText('[OK]|'.dmMediaTools::getAdminUrlFor($object));
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
      $this->getUser()->logAlert(dm::getI18n()->__('Folder %1% is not writable', array('%1%' => $this->folder->getRelPath())));
    }
    else
    {
      $this->folder->delete();
    }

    return $this->redirect(dmMediaTools::getAdminUrlFor($parent));
  }

}