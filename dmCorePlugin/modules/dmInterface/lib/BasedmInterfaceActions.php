<?php

abstract class BasedmInterfaceActions extends dmBaseActions
{

  public function executeLoadMediaFolder(dmWebRequest $request)
  {
    $this->folder = null;
    
    if ($request->getParameter('folder_id'))
    {
      $this->folder = dmDb::table('DmMediaFolder')->find($request->getParameter('folder_id'));
    }
      
    if(!$this->folder)
    {
      $this->forward404Unless($this->folder = dmDb::table('DmMediaFolder')->checkRoot());
    }
    
    $this->folder->sync();

    $this->getUser()->setAttribute('dm_media_browser_folder_id', $this->folder->get('id'));

    return $this->renderPartial('mediaBarInner');
  }

  abstract public function executeLoadPageTree(dmWebRequest $request);

}