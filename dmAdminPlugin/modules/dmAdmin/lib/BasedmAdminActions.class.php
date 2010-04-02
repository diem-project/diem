<?php

class BasedmAdminActions extends dmAdminBaseActions
{
  public function executeIndex(dmWebRequest $request)
  {
    $this->homepageManager = $this->getService('homepage_manager');

    $this->checkVersion =
        sfConfig::get('dm_web_services_version_check')
    &&  $this->getUser()->can('system')
    &&  $this->getService('diem_version_check')->shouldCheck();

    $this->reportAnonymousData =
        sfConfig::get('dm_web_services_report_anonymous_data')
    &&  $this->getUser()->can('system')
    &&  $this->getService('report_anonymous_data')->shouldSend();
  }

  public function executeModuleType(dmWebRequest $request)
  {
    $this->forward404Unless(
      $type = $this->getModuleTypeBySlug($request->getParameter('moduleTypeName')),
      sprintf('%s is not a module type', $request->getParameter('moduleTypeName'))
    );
    
    $this->menu = $this->getService('admin_module_type_menu')->build($type);
    
    $this->context->getEventDispatcher()->connect('dm.bread_crumb.filter_links', array($this, 'listenToBreadCrumbFilterLinksEvent'));
  }
  
  public function executeModuleSpace(dmWebRequest $request)
  {
    $this->forward404Unless(
      $this->type = $this->getModuleTypeBySlug($request->getParameter('moduleTypeName')),
      sprintf('%s is not a module type', $request->getParameter('moduleTypeName'))
    );
  
    $slug = $request->getParameter('moduleSpaceName');
    foreach($this->type->getSpaces() as $space)
    {
      if (dmString::slugify($space->getPublicName()) == $slug)
      {
        $this->space = $space;
        break;
      }
    }

    $this->forward404Unless(
      isset($this->space),
      sprintf('%s is not a module space in %s type', $request->getParameter('moduleTypeName'), $request->getParameter('moduleTypeName'))
    );

    $this->menu = $this->getService('admin_module_space_menu')->build($this->space);
    
    $this->context->getEventDispatcher()->connect('dm.bread_crumb.filter_links', array($this, 'listenToBreadCrumbFilterLinksEvent'));
  }
  
  public function listenToBreadCrumbFilterLinksEvent(sfEvent $event, array $links)
  {
    if (isset($this->space))
    {
      $links[] = $this->getHelper()->link($this->context->getRouting()->getModuleTypeUrl($this->type))
      ->text($this->getI18n()->__($this->type->getPublicName()));
      
      $links[] = $this->getHelper()->tag('h1', $this->getI18n()->__($this->space->getPublicName()));
    }
    else
    {
      $links[] = $this->getHelper()->tag('h1', $this->getI18n()->__($this->getModuleTypeBySlug($this->getRequest()->getParameter('moduleTypeName'))->getPublicName()));
    }
    
    return $links;
  }
  
  protected function getModuleTypeBySlug($slug)
  {
    foreach($this->context->getModuleManager()->getTypes() as $type)
    {
      if (dmString::slugify($type->getPublicName()) == $slug)
      {
        return $type;
      }
    }
    
    return null;
  }

  public function executeVersionCheck()
  {
    $this->versionCheck = $this->getService('diem_version_check');

    if($this->versionCheck->isUpToDate())
    {
      return $this->renderText('');
    }
  }

  public function executeReportAnonymousData()
  {
    $this->getService('report_anonymous_data')->send();

    return $this->renderText('ok');
  }
}