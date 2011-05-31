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
        if(!sfConfig::get('sf_cache_namespace_callable'))
        {
          sfConfig::set('sf_cache_namespace_callable', array($this, 'generatePageCacheKey'));
        }
        
        $viewCacheManager->addCache('dmFront', 'page', array(
          'withLayout'      => true,
          'lifeTime'        => $pageCacheConfig['life_time'],
          'clientLifeTime'  => $pageCacheConfig['life_time'],
          'contextual'      => false, // useless for page cache, only used for partials & components
        ));
      }
    }
  }

  /**
   * Make the cache key depend on the user culture
   */
  public function generatePageCacheKey($internalUri, $hostName, $vary, $contextualPrefix, sfViewCacheManager $viewCacheManager)
  {
    sfConfig::set('sf_cache_namespace_callable', null);
    $cacheKey = $viewCacheManager->generateCacheKey($internalUri, $hostName, $vary, $contextualPrefix);
    sfConfig::set('sf_cache_namespace_callable', array($this, 'generatePageCacheKey'));

    if(strpos($internalUri, '@sf_cache_partial') === 0)
    {
      return $cacheKey;
    }

    $cacheKey = $this->user->getCulture() .':'. $cacheKey;

    sfConfig::set('dm_internal_page_cached', $viewCacheManager->getCache()->has($cacheKey));

    return $cacheKey;
  }

  protected function shouldEnablePageCache()
  {
    return $this->context->getEventDispatcher()->filter(
      new sfEvent($this, 'dm.page_cache.enable', array('context' => $this->context)),
      // by default, the page is cached only for non-authenticated users
      !$this->user->isAuthenticated()
    )->getReturnValue();
  }

  protected function replaceH1()
  {
    if (($page = $this->context->getPage()) && ($h1 = $page->_getI18n('h1')))
    {
      $content = preg_replace(
        '|<h1(.*)>.*</h1>|iuU',
        '<h1$1>'.$h1.'</h1>',
        $this->response->getContent()
      );
      
      if (!$content) // if UTF-8 problem, relunch preg_replace without option 'u'
      {
        $content = preg_replace(
        '|<h1(.*)>.*</h1>|iU',
        '<h1$1>'.$h1.'</h1>',
        $this->response->getContent()
        );
      }
      
      $this->response->setContent($content);
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