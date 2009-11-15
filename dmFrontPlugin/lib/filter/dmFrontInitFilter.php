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
    $culture = $this->context->getUser()->getCulture();
    
    if ($this->context->isModuleAction('dmFront', 'page'))
    {
      $slug = $this->context->getRequest()->getParameter('slug');

      $timer = dmDebug::timerOrNull('dmFrontInitFilter::fetchPage');
      $page = dmDb::query('DmPage p, p.Translation t')
      ->where('t.slug = ?', $slug)
      ->andWhere('t.lang = ?', $culture)
      ->fetchOne();
      $timer->addTime();
      
      if (!$page)
      {
        throw new dmPageNotFoundException(sprintf('There is no page with slug %s in %s culture', $slug, $culture));
      }
    }
    elseif($this->context->getRequest()->hasParameter('dm_cpi'))
    {
      $page = dmDb::query('DmPage p')
      ->where('p.id = ?', $this->context->getRequest()->getParameter('dm_cpi'))
      ->leftJoin('p.Translation t ON p.id = t.id AND t.lang = ?', $culture)
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
    $handler = $this->context->get('page_not_found_handler');
    
    $slug = $this->context->getRequest()->getParameter('slug');
    
    if ($redirectionUrl = $handler->getRedirection($slug))
    {
      return $this->context->getController()->redirect($redirectionUrl, 301);
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