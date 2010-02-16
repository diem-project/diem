<?php

class BasedmPageActions extends dmAdminBaseActions
{

  public function executeIndex(dmWebRequest $request)
  {
    $this->redirect('dmPage/reorderPages');
  }

  public function executeManageMetas()
  {
    $this->pages = dmDb::query('DmPage p')
    ->withI18n()
    ->select('p.id, p.lft, p.level, p.action, pTranslation.name as name, pTranslation.slug as slug, pTranslation.title as title, pTranslation.h1 as h1, pTranslation.description as description, pTranslation.is_active as is_active')
    ->fetchArray();

    $this->fields = array('lft', 'name', 'slug', 'title', 'h1', 'description', 'is_active');

    $this->pageMetaView = new dmAdminPageMetaView($this->getHelper(), $this->getI18n());
  }

  public function executeEditField(sfWebRequest $request)
  {
    $this->forward404Unless($page = dmDb::table('DmPage')->find($request->getParameter('page_id')));
    $field = $request->getParameter('field');

    $page->set($field, $request->getParameter('value'));
    $page->save();

    return $this->renderText($page->get($field));
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