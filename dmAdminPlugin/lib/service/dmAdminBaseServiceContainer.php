<?php

abstract class dmAdminBaseServiceContainer extends dmBaseServiceContainer
{
  protected function loadDependencies(array $dependencies)
  {
    parent::loadDependencies($dependencies);
    
    $this->setService('routing', $dependencies['context']->getRouting());
  }
  
  protected function connectServices()
  {
    parent::connectServices();
    
    $this->getService('bread_crumb')->connect();
  }
  
  
  public function getGapi()
  {
    $user = $this->getService('user');
    $this->setParameter('gapi.email', dmConfig::get('ga_email'));
    $this->setParameter('gapi.password', dmConfig::get('ga_password'));
    $this->setParameter('gapi.auth_token', $user->getAttribute('gapi_auth_token'));
    $gapi = $this->getGapiService();
    $gapi->setCacheManager($this->getCacheManagerService());
    $user->setAttribute('gapi_auth_token', $gapi->getAuthToken());
    return $gapi;
  }
}