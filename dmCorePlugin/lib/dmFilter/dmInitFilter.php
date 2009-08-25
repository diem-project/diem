<?php

abstract class dmInitFilter extends dmFilter
{

  protected function checkFilesystemPermissions()
  {
  	return dmProject::checkFilesystemPermissions();
  }
  
  protected function saveApplicationUrl()
  {
  	if ($site = dmContext::getInstance()->getSite())
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
      $this->getContext()->getResponse()->getContent(),
      10
    );
  }

  protected function saveSession()
  {
    $length = strlen($this->getContext()->getResponse()->getContent());
    $time = round((microtime(true) - dm::getStartTime()) * 100);

    dmDb::table('DmSession')->getCurrent($_SERVER)
    ->update($_SERVER, $time, $length, false /*$this->isPageInCache()*/)
    ->save();
  }
  
  protected function redirectTrailingSlash()
  {
  	$uri = $this->getContext()->getRequest()->getUri();
  	$uriLastChar = substr($uri, -1);
  	
  	if ($uriLastChar === '/')
  	{
	    if ($uri != ($this->getContext()->getRequest()->getAbsoluteUrlRoot().'/'))
	    {
	    	$this->getContext()->getController()->redirect(rtrim($uri, '/'), 0, 302);
	    }
  	}
  }

}