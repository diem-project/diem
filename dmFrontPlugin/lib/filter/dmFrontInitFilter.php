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

    $this->saveApplicationUrl();
    
    // ajax calls use dm_cpi to request a page
    if($pageId = $this->context->getRequest()->getParameter('dm_cpi'))
    {
      if (!$page = dmDb::table('DmPage')->findOneByIdWithI18n($pageId))
      {
        throw new dmException(sprintf('There is no page with id %s', $pageId));
      }
      
      $this->context->setPage($page);
    }
    
    $filterChain->execute();
  }

  protected function redirectNoScriptName()
  {
    if (!sfConfig::get('sf_no_script_name'))
    {
      return;
    }
    
    $request = $this->getContext()->getRequest();
    $absoluteUrlRoot = $request->getAbsoluteUrlRoot();
  
    if (0 === strpos($request->getUri(), $absoluteUrlRoot.'/index.php'))
    {
      $this->context->getController()->redirect(
        str_replace($absoluteUrlRoot.'/index.php', $absoluteUrlRoot, $request->getUri()),
        0,
        302
      );
    }
  }
  
}