<?php

class BasedmPageActions extends dmAdminBaseActions
{

  public function executeIndex(dmWebRequest $request)
  {
    $this->redirect('dmPage/reorderPages');
  }

  public function executeManageMetas(sfWebRequest $request)
  {
    $this->pageMetaView = new dmAdminPageMetaView($this->getHelper(), $this->getI18n());

    $this->form = new dmAdminPageMetaFieldsForm($this->pageMetaView);

    if($request->hasParameter($this->form->getName()))
    {
      $this->form->bindRequest($request);
      $this->fields = $this->form->getValue('fields');
    }
    else
    {
      $this->fields = $this->form->getDefault('fields');
    }

    $query = dmDb::query('DmPage p')
    ->withI18n()
    ->select('p.id, p.lft, p.level, p.action')
    ->orderBy('p.lft ASC');

    foreach($this->fields as $field)
    {
      if('lft' !== $field)
      {
        $query->addSelect('pTranslation.'.$field.' as '.$field);
      }
    }

    $this->pages = $query->fetchArray();
  }

  public function executeEditField(sfWebRequest $request)
  {
    $this->forward404Unless($page = dmDb::table('DmPage')->find($request->getParameter('page_id')));
    $field = $request->getParameter('field');

    $page->set($field, $request->getParameter('value'));
    $page->updateAutoModFromModified()->save();

    return $this->renderText($page->get($field));
  }

  public function executeToggleBoolean(dmWebRequest $request)
  {
    $this->forward404Unless($page = dmDb::table('DmPage')->find($request->getParameter('page_id')));
    $field = $request->getParameter('field');

    if('is_active' === $field)
    {
      $page->setIsActiveManually(!$page->get($field));
    }
    else
    {
      $page->set($field, !$page->get($field));
    }

    $page->save();

    return $this->renderText($page->$field ? '1' : '0');
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