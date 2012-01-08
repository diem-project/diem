<?php

class BasedmMediaActions extends dmAdminBaseActions
{

  public function executePreview(sfWebRequest $request)
  {
    $this->forward404Unless(
      $media = dmDb::table('DmMedia')->findOneByIdWithFolder($request->getParameter('id'))
    );

    return $this->renderPartial('dmMedia/viewBig', array('object' => $media));
  }
  
  public function executeGallery(dmWebRequest $request)
  {
    $this->record = $this->getGalleryRecord($request);

    $formClass = $this->record->getGalleryFormClass();
    $this->form = new $formClass(null, array('mime_types' => 'web_images'));
    $this->form->setDefault('dm_media_folder_id', $this->record->getDmMediaFolder()->get('id'));

    if ($request->isMethod('post') && $this->form->bindAndValid($request))
    {
      $media = $this->form->save();

      $this->record->addMedia($media);
      
      $this->getUser()->logInfo('The item was updated successfully.');
      return $this->redirectBack();
    }
    
    $this->getService('bread_crumb')->setRecord($this->record);
    $this->context->getEventDispatcher()->connect('dm.bread_crumb.filter_links', array($this, 'listenToBreadCrumbFilterLinksEvent'));
    
    $this->galleryOptions = array(
      'model' => get_class($this->record),
      'pk'    => $this->record->getPrimaryKey()
    );
    
    $this->medias = dmDb::query($this->record->getGalleryMediaClass().' m, m.Folder f, m.'.$this->record->getGalleryRelClass().' rel')
    ->where('rel.dm_record_id = ?', $this->record->get('id'))
    ->orderBy('rel.position ASC')
    ->select('m.*, f.*, rel.id as dm_gallery_rel_id')
    ->fetchRecords();

    $this->addByIdForm = new AddMediaByIdForm($this->record);
  }

  public function executeAddToGalleryById(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));

    $form = new AddMediaByIdForm();
    if($form->bindAndValid($request))
    {
      $this->getUser()->logInfo('The item was updated successfully.');
      $form->save();
    }

    return $this->redirectBack();
  }
  
  public function executeSortGallery(dmWebRequest $request)
  {
    $this
    ->getGalleryRecord($request)
    ->getTable()
    ->getRelationHolder()
    ->get('Medias')
    ->getAssociationTable()
    ->doSort(array_flip($request->getParameter('dm_sort')));
    
    return $this->renderText('ok');
  }
  
  public function executeGalleryDelete(dmWebRequest $request)
  {
    $record = $this->getGalleryRecord($request);
    
    $this->forward404Unless(
      $relObject = dmDb::table($record->getGalleryRelClass())->find($request->getParameter('rel_id'))
    );
    
    $this->forwardSecureUnless(
      $relObject->get('dm_record_id') === $record->get('id')
    );
    
    $relObject->delete();
    
    $this->getUser()->logInfo($this->getI18n()->__('The item was deleted successfully.'));
    
    return $this->redirectBack();
  }
  

  protected function getGalleryRecord(dmWebRequest $request)
  {
    $this->forward404Unless(
      $record = dmDb::table($request->getParameter('model'))->find($request->getParameter('pk')),
      'Record not found'
    );

    $this->forward404Unless(
      $module = $record->getDmModule(),
      'Module not found'
    );

    $this->forwardSecureUnless($this->getUser()->canAccessToModule($module));
    
    if (!$record->getTable()->hasTemplate('DmGallery'))
    {
      throw new dmException($record.' should act as DmGallery');
    }
    
    return $record;
  }
  
  public function listenToBreadCrumbFilterLinksEvent(sfEvent $event, array $links)
  {
    $links[] = $this->getHelper()->tag('h1', $this->getI18n()->__('Gallery'));
    
    return $links;
  }
  
}
