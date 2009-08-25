<?php

abstract class dmWebResponse extends sfWebResponse
{
	
	protected
	$assetConfig,
	$cdnConfig,
	$javascriptConfig;
	
  public function initialize(sfEventDispatcher $dispatcher, $options = array())
  {
    parent::initialize($dispatcher, $options);
    
    $this->assetConfig = dmAsset::getConfig();
    
    $this->cdnConfig = array(
      'js'  => sfConfig::get('dm_js_cdn', array('enabled' => false)),
      'css' => sfConfig::get('dm_css_cdn', array('enabled' => false))
    );
    
    $this->javascriptConfig = array();
  }
  
  public function getJavascriptConfig()
  {
  	return $this->javascriptConfig;
  }
  
  public function addJavascriptConfig($key, $value)
  {
  	return $this->javascriptConfig[$key] = $value;
  }

	public function isHtml()
	{
	  return strpos($this->getContentType(), 'text/html') === 0;
	}

	public function cacheStylesheets()
	{
		$timer = dmDebug::timer("dmWebResponse::cacheStylesheets()");
		
		$cachedStylesheets = $this->getCachedStylesheets();
		
		/*
		 * Init stylesheets
		 */ 
    $this->stylesheets = array_combine($this->positions, array_fill(0, count($this->positions), array()));

    foreach($cachedStylesheets as $cachedStylesheet)
    {
    	$this->stylesheets[''][$cachedStylesheet] = array();
    }

    $timer->addTime();
	}

	protected function getCachedStylesheets()
	{
    $name = 'css';
    $stylesheets = array_keys($this->getStylesheets());
    $cachedStylesheets = array();
    $key = '';
    $webDir = sfConfig::get('sf_web_dir');

    foreach($stylesheets as $webPath)
    {
    	if ($this->isStylesheetCachable($webPath))
    	{
        $key .= $webPath . filemtime($webDir.$webPath);
    	}
    	else
    	{
        $cachedStylesheets[] = $webPath;
    	}
    }
    
    $key .= dm::getRequest()->getRelativeUrlRoot();
    $key = md5($key);

		$cacheWebPath = '/cache/'.$name;
		$cacheDirPath = $webDir.$cacheWebPath;
    $cacheFilePath = $cacheDirPath.'/'.$key.'.css';
		$fs = new dmFilesystem();
    $fs->mkdir(sfConfig::get('sf_cache_dir').'/web');
    $fs->mkdir(sfConfig::get('sf_cache_dir').'/web/css');

//    $cssPaths = array('/css/', '/dm/core/css/', '/dm/admin/css', '/dm/front/css');
    $cssPaths = array(sfConfig::get('sf_root_dir'), 'plugins/', 'public_html/', 'web/');

    if(!file_exists($cacheFilePath))
    {
    	$cacheContent = '';
      foreach($stylesheets as $webPath)
      {
	      if ($this->isStylesheetCachable($webPath))
	      {
	        $cacheContent .= $this->fixCssPaths(
	          file_get_contents($webDir.$webPath),
	          str_replace($cssPaths, '', $webPath)
	        );
	      }
      }

	    if (true)
	    {
	      // remove comments, tabs, spaces, newlines, etc.
		    $cacheContent = dmCssMinifier::transform($cacheContent);
	    }

      file_put_contents($cacheFilePath, $cacheContent);
	    file_put_contents($cacheFilePath.'.gz', gzencode($cacheContent));
      chmod($cacheFilePath, 0666);
      chmod($cacheFilePath.'.gz', 0666);
    }

    array_unshift($cachedStylesheets,  $cacheWebPath.'/'.$key.'.css');
    return $cachedStylesheets;
	}
	
	protected function isStylesheetCachable($stylesheet)
	{
		return true;
	}

  private function fixCssPaths($content, $path)
  {
    if (preg_match_all("/url\(\s?[\'|\"]?(.+)[\'|\"]?\s?\)/ix", $content, $urlMatches) )
    {
      $urlMatches = array_unique( $urlMatches[1] );
      $cssPathArray = explode(DIRECTORY_SEPARATOR, $path);

      //aze::trace($path, $content);

      // pop the css file name
      array_pop( $cssPathArray );
      $cssPathCount   = count( $cssPathArray );

      $sfRelativeUrlRoot = dm::getRequest()->getRelativeUrlRoot();

      foreach( $urlMatches as $match )
      {
        $match = str_replace( array('"', "'"), '', $match );
        // replace path if it is relative
        if ( $match[0] !== '/' && strpos( $match, 'http:' ) === false )
        {
          $relativeCount = substr_count( $match, '../' );
          $cssPathSlice = $relativeCount === 0 ? $cssPathArray : array_slice( $cssPathArray  , 0, $cssPathCount - $relativeCount  );
          $newMatchPath = $sfRelativeUrlRoot.implode('/', $cssPathSlice) . '/' . str_replace('../', '', $match);
          $content = str_replace( $match, $newMatchPath, $content );
        }
      }
    }
    return $content;
  }

  public function cacheJavascripts()
  {
    $timer = dmDebug::timer("dmWebResponse::cacheJavascripts()");
    
    $cachedJavascripts = $this->getCachedJavascripts();
    
    /*
     * Init javascripts
     */
    $this->javascripts = array_combine($this->positions, array_fill(0, count($this->positions), array()));

    foreach($cachedJavascripts as $cachedJavascript)
    {
      $this->javascripts[''][$cachedJavascript] = array();
    }

    $timer->addTime();
  }

  protected function getCachedJavascripts()
  {
    $name = 'js';
    $javascripts = array_keys($this->getJavascripts());
    $cachedJavascripts = array();
    $key = '';
    $webDir = sfConfig::get('sf_web_dir');

    foreach($javascripts as $webPath)
    {
      if ($this->isJavascriptCachable($webPath))
      {
        $key .= $webPath . filemtime($webDir.$webPath);
      }
      else
      {
        $cachedJavascripts[] = $webPath;
      }
    }
    
    $key = md5($key);

    $cacheWebPath = '/cache/'.$name;
    $cacheDirPath = $webDir.$cacheWebPath;
    $cacheFilePath = $cacheDirPath.'/'.$key.'.js';
    $fs = new dmFilesystem();
    $fs->mkdir(sfConfig::get('sf_cache_dir').'/web');
    $fs->mkdir(sfConfig::get('sf_cache_dir').'/web/js');

    if(!file_exists($cacheFilePath))
    {
      $cacheContent = '';
      foreach($javascripts as $javascript)
      {
        if ($this->isJavascriptCachable($javascript))
        {
	      	$fileContent = file_get_contents($webDir.$javascript);
	
	      	if (!strpos($javascript, '.min.') && !strpos($javascript, '.pack.'))
	      	{
	      		$minifier = new JsMinEnh($fileContent);
	      		$fileContent = $minifier->minify();
	      	}
	
	      	$cacheContent .= ';'.$fileContent;
        }
      }

      file_put_contents($cacheFilePath, $cacheContent);
      file_put_contents($cacheFilePath.'.gz', gzencode($cacheContent));
      chmod($cacheFilePath, 0666);
      chmod($cacheFilePath.'.gz', 0666);
    }

    array_push($cachedJavascripts,  $cacheWebPath.'/'.$key.'.js');

    return $cachedJavascripts;
  }
  
  protected function isJavascriptCachable($javascript)
  {
    return $javascript{0} === '/';
  }

  protected function calculateAssetPath($type, $asset)
  {
    if (strpos($asset, 'http://') === 0 || $asset{0} === "/" )
    {
      $path = $asset;
    }
    else
    {
    	if($this->cdnConfig[$type]['enabled'] && isset($this->cdnConfig[$type][$asset]))
    	{
    		$path = $this->cdnConfig[$type][$asset];
    	}
    	elseif (isset($this->assetConfig[$type.'.'.$asset]))
    	{
    		$path = $this->assetConfig[$type.'.'.$asset].'.'.$type;
    	}
      else
      {
        $path = dmAsset::getPathFromWebDir($type, $asset).'.'.$type;
      }
      
      if (strpos($path, '%culture%'))
      {
      	$path = str_replace('%culture%', dm::getUser()->getCulture(), $path);
      }
    }
    
    return $path;
  }

  /**
   * Adds javascript code to the current web response.
   *
   * @param string $file      The JavaScript file
   * @param string $position  Position
   * @param string $options   Javascript options
   */
  public function addJavascript($file, $position = '', $options = array())
  {
    $this->validatePosition($position);

    $file = $this->calculateAssetPath('js', $file);

    $this->javascripts[$position][$file] = $options;
  }

  /**
   * Adds a stylesheet to the current web response.
   *
   * @param string $file      The stylesheet file
   * @param string $position  Position
   * @param string $options   Stylesheet options
   */
  public function addStylesheet($file, $position = '', $options = array())
  {
    $this->validatePosition($position);
    
    $file = $this->calculateAssetPath('css', $file);

    $this->stylesheets[$position][$file] = $options;
  }

}