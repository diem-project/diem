<?php

class BasedmPageActions extends dmAdminBaseActions
{

  public function executeIndex(dmWebRequest $request)
  {
    $this->redirect('dmPage/reorderPages');
  }

  public function executeManageMetas()
  {
    $this->pages = dmDb::query('DmPage p')->withI18n()->fetchRecords();

    $this->fields = array('name', 'title', 'description', 'is_active');
  }

  public function executeTableTranslation()
  {
    $translationFile = realpath(dirname(__FILE__).'/..').'/data/dataTableTranslation/'.$this->getUser()->getCulture().'.txt';

    if(!file_exists($translationFile))
    {
      $translationFile = realpath(dirname(__FILE__).'/..').'/data/dataTableTranslation/en.txt';
    }
    
    return $this->renderText(file_get_contents($translationFile));
  }

  public function executeReorderPages()
  {
    sfConfig::set('dm_pageBar_enabled', false);
    $this->tree = $this->getService('page_tree_view', 'dmAdminFullPageTreeView');
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