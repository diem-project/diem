<?php

abstract class dmInitFilter extends dmFilter
{

  protected function updateLock()
  {
    if(sfConfig::get('dm_locks_enabled') && $this->user->can('admin') && $this->response->isHtmlForHuman())
    {
      dmDb::table('DmLock')->update(array(
        'user_id'   => $this->user->getUserId(),
        'user_name' => $this->user->getUser()->get('username'),
        'time'      => $_SERVER['REQUEST_TIME'],
        'app'       => sfConfig::get('sf_app'),
        'module'    => $this->request->getParameter('module'),
        'action'    => $this->request->getParameter('action'),
        'record_id' => $this->request->getParameter('pk', 0),
        'culture'   => $this->user->getCulture(),
        'url'       => str_replace($this->request->getAbsoluteUrlRoot(), '', $this->request->getUri())
      ));
    }
  }

  protected function loadAssetConfig()
  {
    if ($this->response->isHtmlForHuman())
    {
      $this->response->setAssetConfig($this->context->get('asset_config'));
    }
  }
  
  protected function saveApplicationUrl()
  {
    if(dmConfig::isCli())
    {
      return;
    }
    
    $knownBaseUrls = json_decode(dmConfig::get('base_urls', '[]'), true);
    
    $appUrlKey = implode('-', array(sfConfig::get('sf_app'), sfConfig::get('sf_environment')));
    
    $appUrl    = $this->request->getUriPrefix().$this->request->getScriptName();
      
    if (!isset($knownBaseUrls[$appUrlKey]) || $knownBaseUrls[$appUrlKey] !== $appUrl)
    {
      $knownBaseUrls[$appUrlKey] = $appUrl;
      dmConfig::set('base_urls', json_encode($knownBaseUrls));
    }
  }

  protected function redirectTrailingSlash()
  {
    if(dmConfig::isCli())
    {
      return;
    }
    
    $uri = $this->request->getUri();
    
    if ('/' === substr($uri, -1))
    {
      if ($uri !== ($this->request->getAbsoluteUrlRoot().'/'))
      {
        $this->context->getController()->redirect(rtrim($uri, '/'), 0, 301);
      }
    }
  }

}