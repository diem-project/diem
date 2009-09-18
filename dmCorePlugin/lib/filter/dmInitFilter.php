<?php

abstract class dmInitFilter extends dmFilter
{
  
  protected function logUser()
  {
    if ($this->dmContext->isHtmlForHuman())
    {
      $this->dmContext->getService('user_log')->log($this->dmContext);
    }
  }

  protected function checkFilesystemPermissions()
  {
    return dmProject::checkFilesystemPermissions();
  }
  
  protected function saveApplicationUrl()
  {
    if(sfConfig::get('sf_environment') == 'test')
    {
      return;
    }
    
    $knownBaseUrls = json_decode(dmConfig::get('base_urls', '[]'), true);
    
    $appUrlKey = implode('-', array(sfConfig::get('sf_app'), sfConfig::get('sf_environment')));
    
    $appUrl    = $this->context->getRequest()->getUriPrefix().$this->context->getRequest()->getScriptName();
      
    if (!isset($knownBaseUrls[$appUrlKey]) || $knownBaseUrls[$appUrlKey] !== $appUrl)
    {
      $knownBaseUrls[$appUrlKey] = $appUrl;
      dmConfig::set('base_urls', json_encode($knownBaseUrls));
    }
  }

  protected function saveHtml()
  {
    $this->dmContext->getCacheManager()->getCache("dm/view/html/validate")->set(
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