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

    $this->saveApplicationUrl();
    
    try
    {
      $this->guessPage();
    }
    catch(dmPageNotFoundException $e)
    {
      $this->handlePageNotFound();
    }

    $filterChain->execute();
    
    if ($this->context->getPage())
    {
      if (sfConfig::get('dm_html_validate', true) && $this->getContext()->getUser()->can('html_validate_front') && $this->context->getResponse()->isHtmlForHuman())
      {
        $this->saveHtml();
      }
    }
  }

  protected function guessPage()
  {
    if ($this->context->isModuleAction('dmFront', 'page'))
    {
      $slug = $this->context->getRequest()->getParameter('slug');

      $page = dmDb::query('DmPage p, p.Translation t')
      ->where('t.slug = ? AND t.lang = ?', array($slug, $this->context->getUser()->getCulture()))
      ->fetchOne();

      if (!$page)
      {
        throw new dmPageNotFoundException(sprintf('There is no page with slug %s in %s culture', $slug, $this->context->getUser()->getCulture()));
      }
    }
    elseif($this->context->getRequest()->hasParameter('dm_cpi'))
    {
      $page = dmDb::query('DmPage p')
      ->where('p.id = ?', $this->context->getRequest()->getParameter('dm_cpi'))
      ->withI18n()
      ->fetchOne();

      if (!$page)
      {
        throw new dmException(sprintf('There is no page with id %s', $this->context->getRequest()->getParameter('dm_cpi')));
      }
    }
    else
    {
      $page = null;
    }

    if ($page)
    {
      $this->context->setPage($page);
    }
  }
  
  protected function handlePageNotFound()
  {
    if ($slug = $this->context->getRequest()->getParameter('slug'))
    {
      if ($redirection = dmDb::query('DmRedirect r')->where('r.source = ?', $slug)->fetchRecord())
      {
        if ($page = dmDb::table('DmPage')->findOneBySource($redirection->dest))
        {
          $url = dmFrontLinkTag::build($page)->getHref();
        }
        else
        {
          $url = $redirection->dest;
        }
        
        return $this->context->getController()->redirect($url, 301);
      }
      
      if (dmConfig::get('smart_404'))
      {
        try
        {
          $searchIndex = $this->context->get('search_engine')->getCurrentIndex();
          
          $query = Zend_Search_Lucene_Search_QueryParser::parse(
            str_replace('/', ' ', dmString::unSlugify($slug))
          );
          
          $results = $searchIndex->search($query);
          
          if ($result = dmArray::first($results))
          {
            if ($result->getScore() > 0.5)
            {
              return $this->context->getController()->redirect(dmFrontLinkTag::build($result->getPage())->getHref(), 301);
            }
          }
        }
        catch(Exception $e)
        {
          if(sfConfig::get('sf_debug'))
          {
            throw $e;
          }
        }
      }
    }
    
    return $this->forwardTo404Page();
  }

  protected function forwardTo404Page()
  {
    dmDb::table('DmPage')->checkBasicPages();
    
    $page = dmDb::query('DmPage p')
    ->where('p.module = ? AND p.action = ?', array('main', 'error404'))
    ->withI18n()
    ->fetchOne();
    
    $this->context->setPage($page);
  }
  
}