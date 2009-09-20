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
      if (sfConfig::get('dm_html_validate', true) && $this->getContext()->getUser()->can('html_validate_front') && $this->context->isHtmlForHuman())
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