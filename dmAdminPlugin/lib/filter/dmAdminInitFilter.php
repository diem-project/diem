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
    $this->checkFilesystemPermissions();
    
    $this->saveApplicationUrl();

    $this->loadAssetConfig();

    $this->updateLock();

    $filterChain->execute();

    // If response has no title, generate one with the H1
    if ($this->response->isHtmlForHuman() && !$this->response->getTitle())
    {
      preg_match('|<h1[^>]*>(.*)</h1>|iuUx', $this->response->getContent(), $matches);
        
      if (isset($matches[1]))
      {
        $title = 'Admin : '.strip_tags($matches[1]).' - '.dmConfig::get('site_name');
      
        $this->response->setContent(str_replace('<title></title>', '<title>'.$title.'</title>', $this->response->getContent()));
      }
    }
  }

}