<?php

class dmSitemapActions extends dmAdminBaseActions
{
  
  public function preExecute()
  {
    $this->sitemap = $this->getDmContext()->getService('sitemap');
    
    $this->sitemap->setBaseUrl($this->getRequest()->getAbsoluteUrlRoot());
  }
  
  public function executeIndex(dmWebRequest $request)
  {
    if ($this->sitemap->fileExists())
    {
      $this->exists =     true;
      $this->xml =        $this->sitemap->getFileContent();
      $this->updatedAt =  $this->sitemap->getUpdatedAt();
      $this->nbLinks =    $this->sitemap->countUrls();
      $this->size =       dmOs::humanizeSize($this->sitemap->getFileSize());
      $this->webPath =    $this->sitemap->getWebPath();
    }
    else
    {
      $this->exists =     false;
    }
  }
  
  public function executeGenerate(dmWebRequest $request)
  {
    try
    {
      $this->sitemap->generate($this->getUser()->getCulture());
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