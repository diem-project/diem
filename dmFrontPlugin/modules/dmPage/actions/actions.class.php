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
      $page->Node->isRoot(),
      'Can not delete root page'
    );
    
    $this->forward404If(
      $page->hasRecord(),
      'Can not delete record page. Delete record instead.'
    );
    
    $redirectUrl = dmFrontLinkTag::build($page->Node->getParent())->getHref();
    
    $page->delete();
    
    return $this->redirect($redirectUrl);
  }
  
  public function executeEdit(dmWebRequest $request)
  {
    $this->forward404Unless(
      $this->page = $this->context->getPage(),
      'no current DmPage'
    );
    
    $this->form = new DmPageFrontEditForm($this->page);
    
    if ($request->isMethod('put'))
    {
      $this->form->bind();

      if ($this->form->isValid())
      {
        $this->form->updateObject();
        $this->page = $this->form->getObject();
        
        $this->page->updateAutoModFromModified();
        $this->page->save();
        
        /*
         * dmPageView.dmLayoutId may be modified
         */
        $this->page->PageView->save();
        
        return $this->renderJson(array(
          'type'  => 'redirect',
          'url'   => dmFrontLinkTag::build($this->page)->getHref()
        ));
      }
      
      $js = false;
    }
    else
    {
      $js = dmJsMinifier::transform(
        file_get_contents(dmOs::join(sfConfig::get('sf_web_dir'), sfConfig::get('dm_front_asset'), 'js/dmFrontPageEditForm.js'))
      );
    }
    
    $this->css = dmCssMinifier::transform(
      file_get_contents(dmOs::join(sfConfig::get('sf_web_dir'), sfConfig::get('dm_front_asset'), 'css/pageEditForm.css'))
    );
    
    return $this->renderJson(array(
      'type' => 'form',
      'js'   => $js,
      'html' => $this->getPartial('dmPage/edit')
    ));
  }
  
  public function executeNew(dmWebRequest $request)
  {
    $this->forward404Unless(
      $this->page = $this->context->getPage(),
      'no current DmPage'
    );
    
    $this->form = new DmPageFrontNewForm();
    
    if ($request->isMethod('post'))
    {
      $this->form->bind();

      if ($this->form->isValid())
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
        file_get_contents(dmOs::join(sfConfig::get('sf_web_dir'), sfConfig::get('dm_front_asset'), 'js/dmFrontPageAddForm.js'))
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