<?php

class dmXmlSitemapGenerator extends dmConfigurable
{
  protected
  $dispatcher,
  $filesystem,
  $i18n;
  
  public function __construct(sfEventDispatcher $dispatcher, dmFilesystem $filesystem, dmI18n $i18n, array $options)
  {
    $this->dispatcher = $dispatcher;
    $this->filesystem = $filesystem;
    $this->i18n       = $i18n;
    
    $this->initialize($options);
  }

  /*
   * Generates a sitemap
   * and save it in fullPath
   */
  public function execute()
  {
    $this->checkBaseUrl();

    if($this->i18n->hasManyCultures())
    {
      $this->write('sitemap.xml', $this->getIndexXml($this->i18n->getCultures()));

      foreach($this->i18n->getCultures() as $culture)
      {
        $this->write('sitemap_'.$culture.'.xml', $this->getSitemapXml($culture));
      }
    }
    else
    {
      $this->write('sitemap.xml', $this->getSitemapXml($this->i18n->getCulture()));
    }

    $this->dispatcher->notify(new sfEvent($this, 'dm.sitemap.generated', array(
      'dir'     => $this->getOption('dir'),
      'domain'  => $this->getOption('domain'))
    ));
  }

  public function getDefaultOptions()
  {
    return array(
      'dir' => sfConfig::get('sf_web_dir')
    );
  }

  public function getFiles()
  {
    $files = array($this->getOption('dir').'/sitemap.xml');

    if($this->i18n->hasManyCultures())
    {
      foreach($this->i18n->getCultures() as $culture)
      {
        $files[] = $this->getOption('dir').'/sitemap_'.$culture.'.xml';
      }
    }

    return $files;
  }

  public function delete()
  {
    $this->filesystem->unlink($this->getFiles());
  }
  
  protected function initialize(array $options)
  {
    $this->configure($options);
  }

  protected function getIndexXml(array $cultures)
  {
    return sprintf('<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
%s
</sitemapindex>
', $this->getIndexSitemaps($cultures));
  }

  protected function getIndexSitemaps(array $cultures)
  {
    $sitemaps = array();

    foreach($cultures as $culture)
    {
      $sitemaps[] = sprintf('  <sitemap>
    <loc>%s</loc>
  </sitemap>',
      $this->getOption('domain').'/sitemap_'.$culture.'.xml');
    }

    return implode("\n", $sitemaps);
  }
  
  protected function getSitemapXml($culture)
  {
    return sprintf('<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
%s
</urlset>',
    $this->getUrls($this->getPages($culture), $culture)
    );
  }

  /*
   * Wich pages should figure on sitemap ?
   * @return array of dmPage objects
   */
  protected function getPages($culture)
  {
    return dmDb::query('DmPage p')
    ->withI18n($culture)
    ->where('pTranslation.is_secure = ?', false)
    ->addWhere('pTranslation.is_active = ?', true)
    ->addWhere('pTranslation.is_indexable = ?', true)
    ->addWhere('p.module != ? OR ( p.action != ? AND p.action != ? AND p.action != ?)', array('main', 'error404', 'search', 'signin'))
    ->orderBy('p.lft asc')
    ->fetchRecords();
  }
  
  protected function getUrls(myDoctrineCollection $pages, $culture)
  {
    $urls = array();

    foreach($pages as $page)
    {
      $urls[] = $this->getUrl($page, $culture);
    }
    
    return implode("\n", $urls);
  }
  
  protected function getUrl(dmPage $page, $culture)
  {
    return sprintf('  <url>
    <loc>
      %s
    </loc>
  </url>', $this->getOption('domain').'/'.$page->get('Translation')->get($culture)->get('slug'));
  }

  protected function write($filePath, $xml)
  {
    $file = dmOs::join($this->getOption('dir'), $filePath);
    
    if(!file_put_contents($file, $xml))
    {
      throw new dmException('Can not save xml sitemap to '.dmProject::unRootify($file));
    }

    @$this->filesystem->chmod($file, 0666);
  }
  
  public function getUpdatedAt($file)
  {
    $this->checkFileExists($file);

    return filemtime($file);
  }
  
  public function countUrls($file)
  {
    $this->checkFileExists($file);
    
    return substr_count(file_get_contents($file), '<loc>');
  }
  
  public function getFileSize($file)
  {
    $this->checkFileExists($file);
    
    return round(filesize($file) / 1024, 2).' KB';
  }
  
  public function getWebPath($file)
  {
    $this->checkBaseUrl();
    
    return $this->getOption('domain').str_replace(sfConfig::get('sf_web_dir'), '', $file);
  }
  
  protected function checkFileExists($file = null)
  {
    $file = $file ? $file : $this->getOption('dir').'/sitemap.xml';
    
    if (!file_exists($file))
    {
      throw new dmException(sprintf('The sitemap file does not exists'));
    }
  }
  
  protected function checkBaseUrl()
  {
    if (!$this->getOption('domain'))
    {
      throw new dmException('You must give a domain option like www.my-domain.com');
    }
  }
}

class dmSitemapNotWritableException extends dmException
{
  
}