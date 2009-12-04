<?php

class BasedmFrontActions extends dmFrontBaseActions
{
  
  public function executePage(dmWebRequest $request)
  {
    $slug = $request->getParameter('slug');

    $this->page = dmDb::table('DmPage')->findOneBySlug($slug);
    
    // the page does not exist
    if (!$this->page)
    {
      // if page_not_found_handler suggest a redirection
      if ($redirectionUrl = $this->context->get('page_not_found_handler')->getRedirection($slug))
      {
        return $this->redirect($redirectionUrl, 301);
      }
      
      // else use main.error404 page
      $this->page = dmDb::table('DmPage')->fetchError404();
    }
    
    if (
          // the site is not active and requires the view_site permission to be displayed
          (!dmConfig::get('site_active') && !$this->getUser()->can('view_site'))
          // the page is secured and requires authentication to be displayed
      ||  ($this->page->get('is_secure') && !$this->getUser()->isAuthenticated())
    )
    {
      // use main.login page
      $this->page = dmDb::table('DmPage')->fetchLogin();
    }
     
    return $this->renderPage();
  }
  
  public function executeError404(dmWebRequest $request)
  {
    $this->page = dmDb::table('DmPage')->fetchError404();
    
    return $this->renderPage();
  }
  
  public function executeLogin(dmWebRequest $request)
  {
    $this->page = dmDb::table('DmPage')->fetchLogin();
    
    return $this->renderPage();
  }
  
  public function executeSecure(dmWebRequest $request)
  {
    return $this->executeLogin($request);
  }
  
  protected function renderPage()
  {
    // share current page
    $this->context->setPage($this->page);
    
    if ($this->page->isModuleAction('main', 'error404'))
    {
      $this->response->setStatusCode(404); 
    }
    elseif($this->page->isModuleAction('main', 'login'))
    {
      $this->getResponse()->setStatusCode(401);
    }

    /*
     * BC : catch exceptions when getting the layout template
     * because Diem 5.0.0 < ALPHA6 doesn't have this field in database
     */
    try
    {
      $template = $this->page->getPageView()->getLayout()->get('template');
    }
    catch(Exception $e)
    {
      $template = 'page';
    }
    
    $this->setTemplate($template);
    
    $userLayout = dmProject::rootify('apps/front/modules/dmFront/templates/layout');
    if (file_exists($userLayout.'.php'))
    {
      $this->setLayout($userLayout);
    }
    else
    {
      $this->setLayout(dmOs::join(sfConfig::get('dm_front_dir'), 'modules/dmFront/templates/layout'));
    }

    $this->helper = $this->context->get('page_helper');
    
    $this->isEditMode = $this->getUser()->getIsEditMode();
    
    $this->launchDirectActions();
    
    return sfView::SUCCESS;
  }
  
  public function executeToAdmin(dmWebRequest $request)
  {
    return $this->redirect($this->context->getHelper()->£link('app:admin')->getHref());
  }

  /*
   * If an sfAction exists for the current page module.action,
   * it will be executed
   * If some sfActions exist for the current widgets module.action,
   * they will be executed to
   */
  protected function launchDirectActions()
  {
    $moduleManager = $this->context->getModuleManager();
    
    // Add module action for page
    $moduleActions = array();
    
    if($moduleManager->hasModule($this->page->get('module')))
    {
      $moduleActions[] = $this->page->get('module').'/'.$this->page->get('action').'Page';
    }
    
    // Find module/action for page widgets ( including layout )
    foreach($this->helper->getAreas() as $areaArray)
    {
      foreach($areaArray['Zones'] as $zoneArray)
      {
        foreach($zoneArray['Widgets'] as $widgetArray)
        {
          if($moduleManager->hasModule($widgetArray['module']))
          {
            $widgetModuleAction = $widgetArray['module'].'/'.$widgetArray['action'].'Widget';
            
            if(!in_array($widgetModuleAction, $moduleActions))
            {
              $moduleActions[] = $widgetModuleAction;
            }
          }
        }
      }
    }

    foreach($moduleActions as $moduleAction)
    {
      list($module, $action) = explode("/", $moduleAction);

      if ($moduleManager->getModule($module)->hasDirectAction($action))
      {
        $actionToRun = 'execute'.ucfirst($action);
        
        $this->context->getController()->getAction($module, $action)->$actionToRun($this->getRequest());
      }
    }
  }


  public function executeEditToggle(sfWebRequest $request)
  {
    $this->getUser()->setIsEditMode($request->getParameter('active'));
    
    return $this->renderText('ok');
  }
  
  public function executeShowToolBarToggle(sfWebRequest $request)
  {
    $this->getUser()->setShowToolBar($request->getParameter('active'));
    return $this->renderText('ok');
  }

  public function executeSelectTheme(sfWebRequest $request)
  {
    $this->forward404Unless(
      $theme = $this->context->get('theme_manager')->getTheme($request->getParameter('theme')),
      sprintf('%s is not a valid theme.',
        $request->getParameter('theme')
      )
    );
    
    $this->getUser()->setTheme($theme);

    return $this->redirectBack();
  }
}