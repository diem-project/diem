<?php

class BasedmPageActions extends dmAdminBaseActions
{

  public function executeIndex(dmWebRequest $request)
  {
    $this->redirect('dmPage/tree');
  }

  public function executeTree()
  {
    sfConfig::set('dm_pageBar_enabled', false);
    $this->tree = $this->getService('page_tree_view', 'dmAdminFullPageTreeView');
  }

  public function executeMetas()
  {
    $this->pages = dmDb::table('DmPage')->withI18n()->fetchRecords();

    $fields = array('name', 'title', 'description', '');
  }

  public function executeMove(sfWebRequest $request)
  {
    $this->forward404Unless($page = dmDb::table('DmPage')->find($request->getParameter('page')));

    if($nextToPage = dmDb::table('DmPage')->find($request->getParameter('previous')))
    {
      $page->Node->moveAsNextSiblingOf($nextToPage);
    }
    elseif($inPage = dmDb::table('DmPage')->find($request->getParameter('to')))
    {
      $page->Node->moveAsFirstChildOf($inPage);
    }
    else
    {
      $this->forward404('Bad operation');
    }

    return $this->renderText('ok');
  }
}