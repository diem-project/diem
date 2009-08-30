<?php

class dmSitemapGenerator
{
  protected
  $file,
  $filesystem,
  $baseUrl;	
	
	public function __construct($to = 'sitemap.xml')
	{
    $this->file = dmOs::join(sfConfig::get('sf_web_dir'), $to);
		
		$this->filesystem = new dmFilesystem();
		
		$this->baseUrl = dm::getRequest()->getAbsoluteUrlRoot().'/';
		
		$this->configure();
		
		$this->checkPermissions();
	}
	
	protected function configure()
	{
	}
	
	public function setBaseUrl($baseUrl)
	{
		$this->baseUrl = trim($baseUrl, '/').'/';
	}
	
	/*
	 * Wich pages should figure on sitemap ?
	 * @return array of dmPage objects
	 */
	protected function getPages($culture)
	{
		$query = dmDb::query('DmPage p')->withI18n($culture);
		
		$query
    ->where('p.is_secure = ?', false)
    ->addWhere('translation.is_active = ?', true)
    ->addWhere('p.action != ?', 'error404');
    
    $query
    ->orderBy('p.lft asc');
    
    return $query->fetchRecords();
	}
	
	/*
	 * Generates a sitemap
	 * and save in sf_web_dir./.$to
	 */
	public function generate($culture = null)
	{
		$xml = $this->getXml(is_null($culture) ? dm::getUser()->getCulture() : $culture);
		
		if(!file_put_contents($this->file, $xml))
		{
			throw new dmException('Can not save to '.dmProject::unRootify($this->file));
		}
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
  </url>', $this->baseUrl.$page->slug);
	}
  
  protected function checkPermissions()
  {
    if (!$this->filesystem->mkdir(dirname($this->file)))
    {
      throw new dmException(sprintf('%s is not writable', dirname($this->file)));
    }
    
    if (file_exists($this->file) && !is_writable($this->file))
    {
      throw new dmException(sprintf('%s is not writable', $this->file));
    }
  }
	
}