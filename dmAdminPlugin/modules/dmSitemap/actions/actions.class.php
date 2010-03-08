<?php

class dmSitemapActions extends dmAdminBaseActions
{
  
  public function executeIndex(dmWebRequest $request)
  {
    $this->sitemap = $this->getService('xml_sitemap_generator')
    ->setOption('domain', $this->getRequest()->getAbsoluteUrlRoot());
     
    if ($this->getUser()->can('system'))
    {
      $this->shellUser = dmConfig::canSystemCall() ? exec('whoami') : 'www-data';

      $this->phpCli = dmConfig::canSystemCall() ? sfToolkit::getPhpCli() : '/path/to/php';

      $this->rootDir = sfConfig::get('sf_root_dir');

      $this->domainName = $this->getRequest()->getHost();
    }
  }
  
  public function executeGenerate(dmWebRequest $request)
  {
    $this->sitemap = $this->getService('xml_sitemap_generator')
    ->setOption('domain', $this->getRequest()->getAbsoluteUrlRoot());
    
    try
    {
      $this->sitemap->execute();
      $this->getUser()->logInfo('The sitemap has been successfully generated');
    }
    catch(Exception $e)
    {
      $this->getUser()->logAlert('The sitemap can not be generated');
      
      if(sfConfig::get('sf_debug'))
      {
        throw $e;
      }
    }
    
    return $this->redirect('@dm_sitemap');
  }
    
}