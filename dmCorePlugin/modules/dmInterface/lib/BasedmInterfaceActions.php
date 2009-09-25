<?php

abstract class BasedmInterfaceActions extends dmBaseActions
{

  public function executeLoadMediaFolder(sfWebRequest $request)
  {
    if ($request->getParameter('folder_id'))
    {
      $this->forward404Unless(
        $this->folder = dmDb::table('DmMediaFolder')->find($request->getParameter('folder_id')),
        sprintf('%s is not a valid folder_id', $request->getParameter('folder_id'))
      );
    }
    else
    {
      $this->forward404Unless(
        $this->folder = dmDb::table('DmMediaFolder')->getTree()->fetchRoot(),
        sprintf('folder table has no root !')
      );
    }

    $this->getUser()->setAttribute('dm_media_browser_folder_id', $this->folder->get('id'));

    return $this->renderPartial('mediaBarInner');
  }

  abstract public function executeLoadPageTree(sfWebRequest $request);

}