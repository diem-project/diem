<?php

class dmFrontInitFilter extends dmInitFilter
{
  /**
   * Executes this filter.
   *
   * @param sfFilterChain $filterChain A sfFilterChain instance
   */
  public function execute($filterChain)
  {
    $this->redirectTrailingSlash();

    $this->redirectNoScriptName();

    $this->enablePageCache();

    if(!sfConfig::get('dm_internal_page_cached'))
    {
      $this->loadAssetConfig();

      // ajax calls use dm_cpi or page_id request parameter to set a page
      if($pageId = $this->request->getParameter('dm_cpi', $this->request->getParameter('page_id')))
      {
        if (!$page = dmDb::table('DmPage')->findOneByIdWithI18n($pageId))
        {
          throw new dmException(sprintf('There is no page with id %s', $pageId));
        }

        $this->context->setPage($page);
      }
    }

    $filterChain->execute();

    if(!sfConfig::get('dm_internal_page_cached'))
    {
      $this->saveApplicationUrl();

      $this->replaceH1();
    }
  }

  protected function enablePageCache()
  {
    if(!sfConfig::get('sf_cache'))
    {
      return;
    }
    
    $pageCacheConfig = sfConfig::get('dm_performance_page_cache');

    if($pageCacheConfig && $pageCacheConfig['enabled'] && $viewCacheManager = $this->context->getViewCacheManager())
    {
      if($this->shouldEnablePageCache())
      {
        $viewCacheManager->addCache('dmFront', 'page', array(
          'withLayout'      => true,
          'lifeTime'        => $pageCacheConfig['life_time'],
          'clientLifeTime'  => $pageCacheConfig['life_time'],
          'contextual'      => false, // useless for page cache, only used for partials & components
        ));

        sfConfig::set('dm_internal_page_cached', $viewCacheManager->has($viewCacheManager->getCurrentCacheKey()));
      }
    }
  }

  protected function shouldEnablePageCache()
  {
    return $this->context->getEventDispatcher()->filter(
      new sfEvent($this, 'dm.page_cache.enable', array('context' => $this->context)),
      // by default, the page is cached only for non-authenticated users
      !$this->user->getAttribute('user_id', null, 'dmSecurityUser')
    )->getReturnValue();
  }

  protected function replaceH1()
  {
    if (($page = $this->context->getPage()) && ($h1 = $page->_getI18n('h1')))
    {
      $this->response->setContent(preg_replace(
        '|<h1(.*)>.*</h1>|iuU',
        '<h1$1>'.$h1.'</h1>',
        $this->response->getContent()
      ));
    }
  }

  protected function redirectNoScriptName()
  {
    if (!sfConfig::get('sf_no_script_name') || dmConfig::isCli())
    {
      return;
    }
    
    $absoluteUrlRoot = $this->request->getAbsoluteUrlRoot();
  
    if (0 === strpos($this->request->getUri(), $absoluteUrlRoot.'/index.php'))
    {
      $this->context->getController()->redirect(
        str_replace($absoluteUrlRoot.'/index.php', $absoluteUrlRoot, $this->request->getUri()),
        0,
        301
      );
    }
  }
  
}