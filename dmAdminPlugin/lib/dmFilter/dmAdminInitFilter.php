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
  	if ($culture = $this->getContext()->getRequest()->getParameter('culture'))
  	{
  		$this->getContext()->getUser()->setCulture($culture);
  	}

    $this->checkFilesystemPermissions();
    
    $this->saveApplicationUrl();

    $filterChain->execute();
    
    if (dmContext::getInstance()->isHtmlForHuman())
    {
      if (sfConfig::get('dm_html_validate', true) && $this->getContext()->getUser()->can('html_validate_admin'))
      {
        $this->saveHtml();
      }
    }

    if (sfConfig::get('dm_tracking_enabled'))
    {
    	$this->saveSession();
    }
  }

}