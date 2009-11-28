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

}