<?php

abstract class dmInitFilter extends dmFilter
{
	
	protected function logUser()
	{
		$t = dmDebug::timer('log user');
		$log = new dmUserLog;
		$log->log($this->dmContext);
		$t->addTime();
	}

  protected function checkFilesystemPermissions()
  {
  	return dmProject::checkFilesystemPermissions();
  }
  
  protected function saveApplicationUrl()
  {
  	if ($site = $this->dmContext->getSite())
  	{
  		$appUrlKey = sfConfig::get('sf_app').'-'.sfConfig::get('sf_environment');
  		$appUrl    = $this->context->getRequest()->getUriPrefix().$this->context->getRequest()->getScriptName();
  		
  		$knownAppUrls = json_decode($site->appUrls, true);
  		
  		if (!isset($knownAppUrls[$appUrlKey]) || $knownAppUrls[$appUrlKey] !== $appUrl)
  		{
  			$knownAppUrls[$appUrlKey] = $appUrl;
  			$site->appUrls = json_encode($knownAppUrls);
  			$site->save();
  		}
  	}
  }

  protected function saveHtml()
  {
    dmCacheManager::getCache("dm/view/html/validate")->set(
      session_id(),
      $this->context->getResponse()->getContent(),
      10
    );
  }

  protected function redirectTrailingSlash()
  {
  	$uri = $this->getContext()->getRequest()->getUri();
  	$uriLastChar = substr($uri, -1);
  	
  	if ($uriLastChar === '/')
  	{
	    if ($uri != ($this->getContext()->getRequest()->getAbsoluteUrlRoot().'/'))
	    {
	    	$this->context->getController()->redirect(rtrim($uri, '/'), 0, 302);
	    }
  	}
  }

}