<?php

class dmPageActions extends dmFrontBaseActions
{
  
  public function executeDelete(dmWebRequest $request)
  {
    $this->forward404Unless(
      $page = dmDb::table('DmPage')->find($request->getParameter('id')),
      'no current DmPage'
    );
    
    $this->forward404If(
      $page->getNode()->isRoot(),
      'Can not delete root page'
    );
    
    $this->forward404If(
      $page->hasRecord() && $page->getRecord(),
      'Can not delete record page. Please delete record instead.'
    );
    
    $redirectUrl = $this->getHelper()->£link($page->getNode()->getParent())->getHref();
    
    $page->getNode()->delete();
    
    return $this->redirect($redirectUrl);
  }
  
  public function executeEdit(dmWebRequest $request)
  {
    $this->forward404Unless($this->page = $this->context->getPage(), 'no current DmPage');
    
    $this->form = new DmPageFrontEditForm($this->page);
    
    $this->form->removeCsrfProtection();
    
    if ($this->page->isModuleAction('main', 'login'))
    {
      $this->form->changeToDisabled('is_secure')->setDefault('is_secure', false);
    }
    
    if ($request->isMethod('post') && $this->form->bindAndValid($request))
    {
      $this->page = $this->form
      ->updateObject()
      ->updateAutoModFromModified()
      ->saveGet();

      /*
       * dmPageView.dmLayoutId may be modified
       */
      $this->page->getPageView()->save();

      return $this->renderText($this->getHelper()->£link($this->page)->getAbsoluteHref());
    }
    
    $this->deletePageLink =
        $this->getUser()->can('page_delete')
    &&  !$this->page->getNode()->isRoot()
    &&  (!$this->page->hasRecord() || !$this->page->getRecord());
    
    return $this->renderAsync(array(
      'html'  => $this->getPartial('dmPage/edit'),
      'js'    => array('lib.ui-tabs', 'core.tabForm', 'front.pageEditForm'),
      'css'   => array('lib.ui-tabs', 'front.pageEditForm')
    ), true);
  }
  
  public function executeNew(dmWebRequest $request)
  {
    $this->forward404Unless(
      $this->page = $this->context->getPage(),
      'no current DmPage'
    );
    
    $this->form = new DmPageFrontNewForm();
    
    $this->form->removeCsrfProtection();
    
    if ($request->isMethod('post') && $this->form->bindAndValid($request))
    {
      $newPage = $this->form->save();

      return $this->renderText($this->getHelper()->£link($newPage)->getAbsoluteHref());
    }
    
    $this->form->setDefaults(array(
      'parent_id'     => $this->page->id,
      'dm_layout_id'  => $this->page->PageView->dmLayoutId,
      'slug'          => $this->page->slug ? $this->page->slug.'/?' : '?'
    ));
    
    $_parentSlugs = dmDb::query('DmPage p')
    ->where('p.record_id = 0')
    ->withI18n()
    ->select('p.id, pTranslation.slug')
    ->fetchPDO();
    
    $parentSlugs = array();
    foreach($_parentSlugs as $values)
    {
      $parentSlugs[$values[0]] = $values[1];
    }
    
    $this->parentSlugsJson = json_encode($parentSlugs);
    
    return $this->renderAsync(array(
      'js'   => array('front.pageAddForm'),
      'html' => $this->getPartial('dmPage/new')
    ), true);
  }
  
}