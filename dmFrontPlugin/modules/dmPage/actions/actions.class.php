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
    
    $redirectUrl = dmFrontLinkTag::build($page->getNode()->getParent())->getHref();
    
    $page->getNode()->delete();
    
    return $this->redirect($redirectUrl);
  }
  
  public function executeEdit(dmWebRequest $request)
  {
    $this->forward404Unless(
      $this->page = $this->context->getPage(),
      'no current DmPage'
    );
    
    $this->form = new DmPageFrontEditForm($this->page);
    
    $this->form->removeCsrfProtection();
    
    if ($request->isMethod('post'))
    {
      if ($this->form->bindAndValid($request))
      {
        $this->form->updateObject();
        $this->page = $this->form->getObject();
        
        $this->page->updateAutoModFromModified();
        $this->page->save();
        
        /*
         * dmPageView.dmLayoutId may be modified
         */
        $this->page->getPageView()->save();
        
        return $this->renderJson(array(
          'type'  => 'redirect',
          'url'   => dmFrontLinkTag::build($this->page)->getHref()
        ));
      }
      
      $js = false;
    }
    else
    {
      $js =
      file_get_contents($this->context->get('helper')->getJavascriptFullPath('lib.ui-tabs')).
      dmJsMinifier::transform(
      file_get_contents($this->context->get('helper')->getJavascriptFullPath('core.tabForm')).';'.
      file_get_contents($this->context->get('helper')->getJavascriptFullPath('front.pageEditForm'))
      );
    }
    
    $this->deletePageLink =
        $this->getUser()->can('page_delete')
    &&  !$this->page->getNode()->isRoot()
    &&  (!$this->page->hasRecord() || !$this->page->getRecord());
    
    return $this->renderJson(array(
      'type' => 'form',
      'js'   => $js,
      'html' => $this->getPartial('dmPage/edit'),
      'stylesheets' => array(
        $this->context->get('helper')->getStylesheetWebPath('lib.ui-tabs'),
        $this->context->get('helper')->getStylesheetWebPath('front.pageEditForm')
      )
    ));
  }
  
  public function executeNew(dmWebRequest $request)
  {
    $this->forward404Unless(
      $this->page = $this->context->getPage(),
      'no current DmPage'
    );
    
    $this->form = new DmPageFrontNewForm();
    
    $this->form->removeCsrfProtection();
    
    if ($request->isMethod('post'))
    {
      if ($this->form->bindAndValid($request))
      {
        $this->page = $this->form->save();
        
        $this->page->initializeManualPage();
        
        $this->page->save();
        
        return $this->renderJson(array(
          'type'  => 'redirect',
          'url'   => dmFrontLinkTag::build($this->page)->getHref()
        ));
      }
      
      $js = false;
    }
    else
    {
      $this->form->setDefaults(array(
        'parent_id' => $this->page->id,
        'layout_id' => $this->page->PageView->dmLayoutId,
        'slug' => $this->page->slug ? $this->page->slug.'/?' : '?'
      ));
      
      $js = dmJsMinifier::transform(
        file_get_contents($this->context->get('helper')->getJavascriptFullPath('front.pageAddForm'))
      );
    }
    
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
    
    return $this->renderJson(array(
      'type' => 'form',
      'js'   => $js,
      'html' => $this->getPartial('dmPage/new')
    ));
  }
  
}