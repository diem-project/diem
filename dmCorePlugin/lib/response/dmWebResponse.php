<?php

abstract class dmWebResponse extends sfWebResponse
{
	
	protected
	$assetConfig,
	$cdnConfig,
	$javascriptConfig,
	$user;
	
  public function initialize(sfEventDispatcher $dispatcher, $options = array())
  {
    parent::initialize($dispatcher, $options);
    
    $this->assetConfig = dmAsset::getConfig();
    
    $this->cdnConfig = array(
      'css' => sfConfig::get('dm_css_cdn', array('enabled' => false)),
      'js'  => sfConfig::get('dm_js_cdn', array('enabled' => false))
    );
    
    $this->javascriptConfig = array();
  }
  
  public function setUser(dmUser $user)
  {
    $this->user = $user;
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
      
      if (strpos($path, '%culture%') && $this->user)
      {
      	$path = str_replace('%culture%', $this->user->getCulture(), $path);
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