<?php

class BasedmCoreActions extends dmBaseActions
{
  public function executeW3cValidateHtml()
  {
    $this->doctype = sfConfig::get('dm_w3c_doctype', 'XHTML');

    $this->validator = new dmHtmlValidator($this->context->getCacheManager()->getCache("dm/view/html/validate")->get(session_id()));
  }

  public function executeSelectCulture(dmWebRequest $request)
  {
    $this->forward404Unless(
    $culture = $request->getParameter('culture'),
      'No culture specified'
    );

    $this->forward404Unless(
    $this->context->getI18n()->cultureExists($culture),
    "Culture $culture does not exist"
    );

    $this->getUser()->setCulture($culture);

    return $this->redirectBack();
  }

  public function executeRefresh(dmWebRequest $request)
  {
    $this->next = array(
      'type' => 'ajax',
      'url'  => dmLinkTag::build('+/dmCore/refreshStep?step=1')->getHref(),
      'msg'  => 'Clear the cache'
    );
    
    $this->getUser()->setAttribute('dm_refresh_back_url', $this->getBackUrl());
  }
  
  public function executeRefreshStep(dmWebRequest $request)
  {
    $this->step = $request->getParameter('step');
    
    switch($this->step)
    {
      case 1:
        $this->context->get('cache_manager')->clearAll();
     
        if ($this->getUser()->can('system'))
        {
          $this->context->get('filesystem')->sf('dmFront:generate');
    
          dmFileCache::clearAll();
        }
        
        $data = array(
          'msg'  => $this->context->getI18n()->__('Synchronize pages'),
          'type' => 'ajax',
          'url'  => dmLinkTag::build('+/dmCore/refreshStep?step=2')->getHref()
        );
        break;
        
      case 2:
        $serviceContainer = $this->context->getServiceContainer();
        $threadLauncher = $serviceContainer->getService('thread_launcher');
  
        $pageSynchronizerSuccess = $threadLauncher->execute('dmPageSynchronizerThread', array(
          'class'   => $serviceContainer->getParameter('page_synchronizer.class')
        ));
        
        if (!$pageSynchronizerSuccess)
        {
          dmDebug::showPre($threadLauncher->getLastExec());
          throw new dmException('Error while synchronizing pages');
        }
        
        $data = array(
          'msg'  => $this->context->getI18n()->__('Synchronise SEO'),
          'type' => 'ajax',
          'url'  => dmLinkTag::build('+/dmCore/refreshStep?step=3')->getHref()
        );
        break;
        
      case 3:
        
        $this->context->get('page_tree_watcher')->synchronizePages();
        
        $data = array(
          'msg'  => $this->context->getI18n()->__('Regenerate interface'),
          'type' => 'redirect',
          'url'  => $this->getUser()->getAttribute('dm_refresh_back_url')
        );
        
        $this->context->getEventDispatcher()->notify(new sfEvent($this, 'dm.refresh', array()));
        $this->getUser()->getAttributeHolder()->remove('dm_refresh_back_url');
        $this->getUser()->logInfo('Project successfully updated');
        break;
    }
    
    if ($this->getRequest()->isXmlHttpRequest())
    {
      return $this->renderJson($data);
    }
    
    $this->data = $data;
  }

}