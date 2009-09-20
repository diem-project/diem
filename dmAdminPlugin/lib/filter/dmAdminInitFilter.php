<?php

class dmAdminInitFilter extends dmInitFilter
{

  /**
   * Executes this filter.
   *
   * @param sfFilterChain $filterChain A sfFilterChain instance
   */
  public function execute($filterChain)
  {
    if ($culture = $this->context->getRequest()->getParameter('culture'))
    {
      $this->context->getUser()->setCulture($culture);
    }
    
    $this->checkFilesystemPermissions();
    
    $this->saveApplicationUrl();

    $filterChain->execute();
    
    if ($this->dmContext->isHtmlForHuman())
    {
      if (sfConfig::get('dm_html_validate', true) && $this->context->getUser()->can('html_validate_admin'))
      {
        $this->saveHtml();
      }
    }
  }

}