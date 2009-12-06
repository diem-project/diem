<?php

class dmSitemap
{
  protected
  $dispatcher,
  $filesystem,
  $baseUrl,
  $path;  
  
  public function __construct(sfEventDispatcher $dispatcher, dmFilesystem $filesystem, array $options)
  {
    $this->dispatcher = $dispatcher;
    
    $this->filesystem = $filesystem;
    
    $this->initialize($options);
  }
  
  protected function initialize(array $options)
  {
    $this->setPath(dmArray::get($options, 'file', 'sitemap.xml'));
    
    if(isset($options['baseUrl']))
    {
      $this->setBaseUrl($options['base_url']);
    }

    $this->checkPermissions();
  }
  
  public function setBaseUrl($baseUrl)
  {
    $this->baseUrl = trim($baseUrl, '/').'/';
  }
  
  public function setPath($path)
  {
    $this->path = trim(dmOs::join($path), '/');
  }
  
  public function getPath()
  {
    return $this->path;
  }
  
  public function getFullPath()
  {
    return dmOs::join(sfConfig::get('sf_web_dir'), $this->getPath());
  }
  
  /*
   * Wich pages should figure on sitemap ?
   * @return array of dmPage objects
   */
  protected function getPages($culture)
  {
    $query = dmDb::query('DmPage p')->withI18n($culture)
    ->where('pTranslation.is_secure = ?', false)
    ->addWhere('pTranslation.is_active = ?', true)
    ->addWhere('p.module != ? OR ( p.action != ? AND p.action != ? AND p.action != ?)', array('main', 'error404', 'search', 'login'))
    ->orderBy('p.lft asc');
    
    return $query->fetchRecords();
  }
  
  /*
   * Generates a sitemap
   * and save it in fullPath
   */
  public function generate($culture)
  {
    $this->checkBaseUrl();
    
    $xml = $this->getXml($culture);
    
    if(!file_put_contents($this->getFullPath(), $xml))
    {
      throw new dmException('Can not save to '.dmProject::unRootify($this->getFullPath()));
    }
    
    $this->dispatcher->notify(new sfEvent($this, 'dm.sitemap.generated', array(
      'file' => $this->getFullPath(),
      'url'  => $this->getWebPath()
    )));
  }
  
  protected function getXml($culture)
  {
    return sprintf('<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
%s
</urlset>',
    $this->getUrls($this->getPages($culture))
    );
  }
  
  protected function getUrls(myDoctrineCollection $pages)
  {
    $urls = array();
    foreach($pages as $page)
    {
      $urls[] = $this->getUrl($page);
    }
    
    return implode("\n", $urls);
  }
  
  protected function getUrl(dmPage $page)
  {
    return sprintf('  <url>
    <loc>
      %s
    </loc>
  </url>', $this->baseUrl.$page->_getI18n('slug'));
  }
  
  public function fileExists()
  {
    return file_exists($this->getFullPath());
  }
  
  public function getFileContent()
  {
    $this->checkFileExists();
    return file_get_contents($this->getFullPath());
  }
  
  public function getUpdatedAt()
  {
    $this->checkFileExists();
    return filemtime($this->getFullPath());
  }
  
  public function countUrls()
  {
    $this->checkFileExists();
    return substr_count($this->getFileContent(), '<url>');
  }
  
  public function getFileSize()
  {
    $this->checkFileExists();
    return filesize($this->getFullPath());
  }
  
  public function getWebPath()
  {
    $this->checkBaseUrl();
    return $this->baseUrl.$this->getPath();
  }
  
  protected function checkFileExists()
  {
    if (!$this->fileExists())
    {
      throw new dmException(sprintf('The sitemap file does not exists'));
    }
  }
  
  protected function checkPermissions()
  {
    if (!$this->filesystem->mkdir(dirname($this->getFullPath())))
    {
      throw new dmSitemapNotWritableException(sprintf('%s is not writable', dmProject::unRootify(dirname($this->getFullPath()))));
    }
    
    if (file_exists($this->getFullPath()) && !is_writable($this->getFullPath()))
    {
      throw new dmSitemapNotWritableException(sprintf('%s is not writable', dmProject::unRootify($this->getFullPath())));
    }
  }
  
  protected function checkBaseUrl()
  {
    if (!$this->baseUrl)
    {
      throw new dmException('You must pass a baseUrl');
    }
  }
}

class dmSitemapNotWritableException extends dmException
{
  
}