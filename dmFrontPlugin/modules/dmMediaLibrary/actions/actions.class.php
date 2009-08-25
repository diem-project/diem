<?php

class dmMediaLibraryActions extends dmFrontBaseActions
{

	public function executeEditImage(sfWebRequest $request)
	{
	  $this->forward404Unless(
      $this->file = dm::db('DmMedia')->findPk(
        $request->getParameter('media_id')
      )
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
      'referrer'  => DmSitePeer::getInstance()->getName(),
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
      $this->file = dm::db('DmMedia')->findPk(
        $request->getParameter('media_id')
      )
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
      $this->file = dm::db('DmMedia')->findPk(
        $request->getParameter('media_id')
      )
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
		$path = $request->getParameter('path', '');

    if(!$this->folder = dm::db('DmMediaFolder')->where('RelPath', $path)->findOne())
    {
      $this->getUser()->logError(sprintf('Media folder %s does not exist', $path));

      DmMediaFolderPeer::retrieveRootOrCreate();

      $this->redirect('@dm_media_library_path');
    }

    if (!$this->folder->isWritable())
    {
    	$this->getUser()->logAlert(dm::getI18n()->__('This folder is not writable'));
    }

    $this->folder->sync();

    $this->files = $this->folder->getDmMedias();

    $this->metadata = array();
    if($this->getUser()->hasFlash('dm_media_open'))
    {
    	$this->metadata['open_media'] = $this->getUser()->getFlash('dm_media_open');
    }
	}

  public function executeNewFile(sfWebRequest $request)
  {
    $this->forward404Unless(
      $parent = dm::db('DmMediaFolder')->findPk($request->getParameter('folder_id')),
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
      dm::db('DmMedia')->findPk(
        dmArray::get($request->getParameter('dm_media'), 'id')
      )
    );

    $this->form->bind();

    if ($this->form->isValid())
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
      $this->file = dm::db('DmMedia')->findPk(
        $request->getParameter('media_id')
      )
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
      $this->folder = dm::db('DmMediaFolder')->findPk(
        $request->getParameter('folder_id')
      )
    );

    if(!$parent = $this->folder->retrieveParent())
    {
      throw new dmException();
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
      $parent = dm::db('DmMediaFolder')->findPk($request->getParameter('folder_id')),
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
      dm::db('DmMediaFolder')->findPk(
        dmArray::get($request->getParameter('dm_media_folder'), 'id')
      )
    );

    $this->form->bind();

    if ($this->form->isValid())
    {
    	$oldName = $this->form->getObject()->getName();

    	$object = $this->form->updateObject();

    	$this->forward404Unless(
    	  $parent = dm::db('DmMediaFolder')->findPk($this->form->getValue('parent_id')),
    	  sprintf('There is no parent %d', $this->form->getValue('parent_id'))
    	);

    	if ($object->isNew())
    	{
    	  $object->insertAsLastChildOf($parent);
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
      $this->folder = dm::db('DmMediaFolder')->findPk(
        $request->getParameter('folder_id')
      )
    );

    if(!$parent = $this->folder->retrieveParent())
    {
    	throw new dmException();
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