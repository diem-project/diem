<?php

class dmSitemapActions extends dmAdminBaseActions
{
  
  public function executeIndex(dmWebRequest $request)
  {
    if (file_exists($this->getFile()))
    {
    	$this->sitemap = file_get_contents($this->getFile());
      $this->updatedAt = filemtime($this->getFile());
      $this->nbLinks = substr_count($this->sitemap, '<url>');
      $this->size = dmOs::humanizeSize($this->getFile());
      $this->sitemapWebPath = $this->getRequest()->getAbsoluteUrlRoot().'/'.$this->getFileName();
    }
    else
    {
    	$this->sitemap = false;
    }
  }
  
  public function executeGenerate(dmWebRequest $request)
  {
    $sitemapGenerator = new mySitemapGenerator($this->getFileName());
    
    try
    {
    	$sitemapGenerator->generate();
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
    
  protected function getFile()
  {
  	return dmOs::join(sfConfig::get('sf_web_dir'), $this->getFileName());
  }
  
  protected function getFileName()
  {
  	return trim(dmArray::get(sfConfig::get('dm_seo_sitemap'), 'path', 'sitemap.xml'), '/');
  }
}