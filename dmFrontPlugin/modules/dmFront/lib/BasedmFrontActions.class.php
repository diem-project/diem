<?php

class BasedmFrontActions extends dmFrontBaseActions
{
  
  public function executePage(dmWebRequest $request)
  {
    $slug = $request->getParameter('slug');

    // find matching page_route for this slug
    $pageRoute = $this->getService('page_routing')->find($slug);
    
    if ($pageRoute)
    {
      $this->page = $pageRoute->getPage();
      
      // found a page on another culture
      if($pageRoute->getCulture() !== $this->getUser()->getCulture())
      {
        $this->getUser()->setCulture($pageRoute->getCulture());
      }
    }
    // the page does not exist
    else
    {
      // if page_not_found_handler suggest a redirection
      if ($redirectionUrl = $this->getService('page_not_found_handler')->getRedirection($slug))
      {
        return $this->redirect($redirectionUrl, 301);
      }
      
      // else use main.error404 page
      $this->page = dmDb::table('DmPage')->fetchError404();
    }

    $this->secure();
     
    return $this->renderPage();
  }

  protected function secure()
  {
    if (
          // the site is not active and requires the view_site permission to be displayed
          (!dmConfig::get('site_active') && !$this->getUser()->can('site_view'))
          // the page is not active and requires the view_site permission to be displayed
      ||  (!$this->page->get('is_active') && !$this->getUser()->can('site_view'))
          // the page is secured and requires authentication to be displayed
      ||  ($this->page->get('is_secure') && !$this->getUser()->isAuthenticated())
          // the page is secured and the user has not required credentials
      ||  ($this->page->get('is_secure') && $this->page->get('credentials') && !$this->getUser()->can($this->page->get('credentials')))
    )
    {
      // use main.login page
      $this->page = dmDb::table('DmPage')->fetchLogin();
    }
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

    $template = $this->page->getPageView()->getLayout()->get('template');
    
    if (empty($template))
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
    
    $this->helper = $this->getService('page_helper');
    
    $this->isEditMode = $this->getUser()->getIsEditMode();
    
    $this->launchDirectActions();
    
    return sfView::SUCCESS;
  }
  
  public function executeToAdmin(dmWebRequest $request)
  {
    return $this->redirect($this->getHelper()->£link('app:admin')->getHref());
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
    
    $moduleActions = array();

    // Add module action for page
    if($moduleManager->hasModule($this->page->get('module')))
    {
      $moduleActions[] = $this->page->getModuleAction().'Page';
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
      list($module, $action) = explode('/', $moduleAction);

      if ($this->context->getController()->actionExists($module, $action))
      {
        $actionToRun = 'execute'.ucfirst($action);
        
        try
        {
          $this->context->getController()->getAction($module, $action)->$actionToRun($this->getRequest());
        }
        catch(sfControllerException $e)
        {
          $this->getContext()->getLogger()->warning(sprintf('The %s/%s direct action does not exist', $module, $action));
        }
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
      $theme = $this->getService('theme_manager')->getTheme($request->getParameter('theme')),
      sprintf('%s is not a valid theme.',
        $request->getParameter('theme')
      )
    );
    
    $this->getUser()->setTheme($theme);

    return $this->redirectBack();
  }
  
  public function executeSelectCulture(dmWebRequest $request)
  {
    $this->forward404Unless(
      $culture = $request->getParameter('culture'),
      'No culture specified'
    );

    $this->forward404Unless(
      $this->context->getI18n()->cultureExists($culture),
      sprintf('The %s culture does not exist', $culture)
    );

    $this->getUser()->setCulture($culture);
    
    if ($pageId = $request->getParameter('dm_cpi'))
    {
      $this->forward404Unless($page = dmDb::table('DmPage')->findOneByIdWithI18n($pageId));
      
      return $this->redirect($this->getHelper()->£link($page)->getHref());
    }

    return $this->redirectBack();
  }
}