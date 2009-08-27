<?php

class dmPageActions extends dmFrontBaseActions
{
  
  public function executeEdit(dmWebRequest $request)
  {
    $this->forward404Unless(
      $this->page = dmContext::getInstance()->getPage(),
      'no current DmPage'
    );
    
    $this->form = new DmPageFrontEditForm($this->page);
    
    if ($request->isMethod('post'))
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
        
        return $this->renderText(implode('__DM_SPLIT__', array(
          'ok',
          dmFrontLinkTag::build($this->page)->getHref()
        )));
      }
      
      $this->js = false;
    }
    else
    {
      $this->js =
	      dmJsMinifier::transform(
          file_get_contents(dmOs::join(sfConfig::get('sf_web_dir'), sfConfig::get('dm_front_asset'), 'js/dmFrontPageEditForm.js'))
	      )
	    ;
    }
    
    $this->css = dmCssMinifier::transform(
      file_get_contents(dmOs::join(sfConfig::get('sf_web_dir'), sfConfig::get('dm_front_asset'), 'css/pageEditForm.css'))
    );
  }
  
  public function executeNew(dmWebRequest $request)
  {
    $this->forward404Unless(
      $this->page = dmContext::getInstance()->getPage(),
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
        
        return $this->renderText(implode('__DM_SPLIT__', array(
          'ok',
          dmFrontLinkTag::build($this->page)->getHref()
        )));
      }
      
      $this->js = false;
    }
    else
    {
      $this->form->setDefaults(array(
        'parent_id' => $this->page->id,
        'layout_id' => $this->page->PageView->dmLayoutId,
        'slug' => $this->page->slug ? $this->page->slug.'/?' : '?'
      ));
      
      $this->js = dmJsMinifier::transform(
        file_get_contents(dmOs::join(sfConfig::get('sf_web_dir'), sfConfig::get('dm_front_asset'), 'js/dmFrontPageAddForm.js'))
      );
    }
    
    $_parentSlugs = dmDb::query('DmPage p')
    ->where('p.record_id = 0')
    ->withI18n()
    ->select('p.id, translation.slug')
    ->fetchPDO();
    
    $parentSlugs = array();
    foreach($_parentSlugs as $values)
    {
      $parentSlugs[$values[0]] = $values[1];
    }
    
    $this->parentSlugsJson = json_encode($parentSlugs);
  }
	
}