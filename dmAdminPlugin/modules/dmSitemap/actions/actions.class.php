<?php

class dmSitemapActions extends dmAdminBaseActions
{
  
  public function executeIndex(dmWebRequest $request)
  {
    try
    {
      $this->sitemap = $this->context->get('sitemap');
    }
    catch(dmSitemapNotWritableException $e)
    {
      $this->sitemap = null;
      $this->getUser()->logError($e->getMessage(), false);
      
      if(sfConfig::get('dm_debug'))
      {
        throw $e;
      }
    }
    
    if ($this->sitemap)
    {
      $this->sitemap->setBaseUrl($this->getRequest()->getAbsoluteUrlRoot());
      
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
    else
    {
      return 'NotWritable';
    }
  }
  
  public function executeGenerate(dmWebRequest $request)
  {
    $this->sitemap = $this->context->get('sitemap');
    $this->sitemap->setBaseUrl($this->getRequest()->getAbsoluteUrlRoot());
    
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