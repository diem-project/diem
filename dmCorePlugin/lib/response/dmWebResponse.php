<?php

class dmWebResponse extends sfWebResponse
{
  protected
  $isHtmlForHuman = true,
  $assetAliases,
  $cdnConfig,
  $javascriptConfig,
  $culture,
  $theme,
  $xmlns, // Enhancement: TheCelavi -> holder for custom XML name spaces
  $customHeadTags; // Enhancement: TheCelavi -> holder for custom head tags
          
  public function initialize(sfEventDispatcher $dispatcher, $options = array())
  {
    parent::initialize($dispatcher, $options);
    
    $this->javascriptConfig = array();
    // Enhancement: TheCelavi -> custom XML ns and HEAD tags
    $this->xmlns = array();
    $this->customHeadTags = array();
    // End enhancement
    $this->dispatcher->connect('user.change_culture', array($this, 'listenToChangeCultureEvent'));
    
    $this->dispatcher->connect('user.change_theme', array($this, 'listenToChangeThemeEvent'));
    
    $this->dispatcher->connect('user.remember_me', array($this, 'listenToRememberMeEvent'));
    
    $this->dispatcher->connect('user.sign_out', array($this, 'listenToSignOutEvent'));
  }

  /**
   * Listens to the user.change_culture event.
   *
   * @param sfEvent An sfEvent instance
   */
  public function listenToChangeCultureEvent(sfEvent $event)
  {
    $this->culture = $event['culture'];
  }

  /**
   * Listens to the user.change_theme event.
   *
   * @param sfEvent An sfEvent instance
   */
  public function listenToChangeThemeEvent(sfEvent $event)
  {
    $this->setTheme($event['theme']);
  }
  
  /**
   * Listens to the user.remember_me event.
   *
   * @param sfEvent An sfEvent instance
   */
  public function listenToRememberMeEvent(sfEvent $event)
  {
    $this->setCookie($this->getRememberCookieName(), $event['remember_key'], time() + $event['expiration_age']);
  }
  
  /**
   * Listens to the user.sign_out event.
   *
   * @param sfEvent An sfEvent instance
   */
  public function listenToSignOutEvent(sfEvent $event)
  {
    $this->setCookie($this->getRememberCookieName(), '', time() - $event['expiration_age']);
  }
  
  public function getRememberCookieName()
  {
    return sfConfig::get('dm_security_remember_cookie_name', 'dm_remember_'.dmProject::getHash());
  }
  
  public function setTheme(dmTheme $theme)
  {
    $this->theme = $theme;
  }

  public function getTheme()
  {
    if($this->theme)
    {
      return $this->theme;
    }
    // quick ugly fix
    else
    {
      return sfContext::getInstance()->getServiceContainer()->getParameter('user.theme');
    }
  }

  public function getAssetAliases()
  {
    if (null === $this->assetAliases)
    {
      $this->assetAliases = include(dmContext::getInstance()->get('config_cache')->checkConfig('config/dm/assets.yml'));
    }
    
    return $this->assetAliases;
  } 
  
  /**
   * Sets the asset configuration
   *
   * @param dmAssetConfig the asset configuration
   */
  public function setAssetConfig(dmAssetConfig $assetConfig)
  {
    foreach($assetConfig->getStylesheets() as $stylesheet)
    {
      $this->addStylesheet($stylesheet, 'first');
    }
    
    foreach($assetConfig->getJavascripts() as $javascript)
    {
      $this->addJavascript($javascript, 'first');
    }
  }
  
  public function getCdnConfig()
  {
    if (null === $this->cdnConfig)
    {
      $this->cdnConfig = array(
        'css' => sfConfig::get('dm_css_cdn',  array('enabled' => false)),
        'js'  => sfConfig::get('dm_js_cdn',   array('enabled' => false))
      );
    }
    
    return $this->cdnConfig;
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

  public function calculateAssetPath($type, $asset)
  {
    if ($asset{0} === '/' || strpos($asset, 'http://') === 0)
    {
      $path = $asset;
    }
    else
    {
      $cdnConfig = $this->getCdnConfig();
      $assetAliases = $this->getAssetAliases();
      
      if(isset($cdnConfig[$type]) && $cdnConfig[$type]['enabled'] && isset($cdnConfig[$type][$asset]))
      {
        $path = $cdnConfig[$type][$asset];
      }
      elseif(isset($assetAliases[$type.'.'.$asset]))
      {
        $path = $assetAliases[$type.'.'.$asset];
      }
      elseif($type === 'css')
      {
        $path = $this->getTheme()->getPath('css/'.$asset.'.css');
      }
      else
      {
        $path = '/'.$type.'/'.$asset.'.'.$type;
      }
      
      if (strpos($path, '%culture%') !== false)
      {
        $path = str_replace('%culture%', $this->culture, $path);
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
  public function addJavascript($asset, $position = '', $options = array())
  {
    if(!$this->isHtmlForHuman)
    {
      return $this;
    }

    if(in_array($asset, sfConfig::get('dm_js_head_inclusion')))
    {
      $options['head_inclusion'] = true;
    }
    
    $this->validatePosition($position);

    $file = $this->calculateAssetPath('js', $asset);

    $this->javascripts[$position][$file] = $options;

    return $this;
  }

  /**
   * Adds a stylesheet to the current web response.
   *
   * @param string $file      The stylesheet file
   * @param string $position  Position
   * @param string $options   Stylesheet options
   */
  public function addStylesheet($asset, $position = '', $options = array())
  {
    if(!$this->isHtmlForHuman)
    {
      return $this;
    }
    
    $this->validatePosition($position);
    
    $file = $this->calculateAssetPath('css', $asset);

    $this->stylesheets[$position][$file] = $options;

    return $this;
  }

  public function clearStylesheets()
  {
    $this->stylesheets = array_combine($this->positions, array_fill(0, count($this->positions), array()));

    return $this;
  }

  public function clearJavascripts()
  {
    $this->javascripts = array_combine($this->positions, array_fill(0, count($this->positions), array()));

    return $this;
  }

  public function getHeadJavascripts()
  {
    $headInclusion = sfConfig::get('dm_js_head_inclusion');
    
    if(empty($headInclusion))
    {
      return array();
    }
    
    $javascripts = array();
    foreach($this->getJavascripts() as $webPath => $options)
    {
      if (isset($options['head_inclusion']))
      {
        $javascripts[$webPath] = $options;
      }
    }

    return $javascripts;
  }
  
  /**
   * Means that request has been sent by a human, and the application will send html for a browser.
   * CLI, ajax and flash are NOT human.
   * @return boolean $human
   */
  public function isHtmlForHuman()
  {
    return $this->isHtmlForHuman;
  }
  
  public function setIsHtmlForHuman($val)
  {
    $this->isHtmlForHuman = (bool) $val;
  }
  
  /*
   * Enhancement: The Celavi
   * Support for the custom XML namespaces
   */
  
  /**
   * Add custom XML namespace
   * @param type $namespace - the namespace, the output will be xmlns:$namespace, example: og
   * @param type $schema - path to the schema, add full URI, example: http://ogp.me/ns#
   * 
   * The result, per example, should be: xmlns:og="http://ogp.me/ns#"
   * 
   * @return dmWebResponse instance
   * 
   */
  public function addXmlNs($namespace, $schema) {
      $this->xmlns[$namespace] = $schema;
      return $this;
  }
  /**
   * Sets custom XML namespace
   * @see addXmlNs
   * @param type $namespace
   * @param type $schema 
   * 
   * @return dmWebResponse instance
   */
  public function setXmlNs($namespace, $schema) {
      $this->addXmlNs($namespace, $schema);
      return $this;
  }
  /**
   * Removes custom XML namespace
   * @param type $namespace 
   * 
   * @return dmWebResponse instance
   */
  public function removeXmlNs($namespace) {
      if (isset($this->xmlns[$namespace])) unset($this->xmlns[$namespace]);
      return $this;
  }
  /**
   * Removes all custom XML namespace
   * 
   * @return dmWebResponse instance
   */
  public function clearXmlNs() {
      $this->xmlns = array();
      return $this;
  }
  /**
   * Returns all added custom XML namespaces
   * @return array of added custom XML namespaces
   */
  public function getAllXmlNs() {
      return $this->xmlns;
  }
  /**
   * Returns XML namespace full URI for added custom XML namespace
   * @param string $namespace
   * @return string XML namespace full URI
   */
  public function getXmlNs($namespace) {
      if (isset($this->xmlns[$namespace])) return $this->xmlns[$namespace];
      else return null;
  }

  /*
   * Enhancement: The Celavi
   * Support for the custom HEAD tags
   */
  
  /**
   * Add a custom HEAD tag
   * 
   * example: <tagName attr1="attr_1_val" attr2="attr_2_val">value</tagName>
   * or: <tagName attr1="attr_1_val" attr2="attr_2_val" /> if value is not given
   * 
   * @param string $tagName
   * @param array $attributes
   * @param mixed $value 
   * 
   * @return dmWebResponse instance
   * 
   */
  public function addCustomHeadTag($tagName, $attributes = array(), $value = null) {
      $this->customHeadTags[] = array(
          'tagName'=>$tagName,
          'attributes'=>$attributes,
          'value'=>$value
      );
      return $this;
  }
  /**
   * Sets a custom HEAD tag or adds it if it does not exist
   * 
   * @see addCustomHeadTag
   * @param int $index
   * @param string $tagName
   * @param array $attributes
   * @param mixed $value 
   * 
   * @return dmWebResponse instance
   */
  public function setCustomHeadTagByIndex($index, $tagName, $attributes = array(), $value = null) {
      if (isset($this->customHeadTags[$index])) $this->customHeadTags[$i] = array(
          'tagName'=>$tagName,
          'attributes'=>$attributes,
          'value'=>$value
      ); else $this->addCustomHeadTag ($tagName, $attributes, $value);
      return $this;
  }
  
  /**
   * Removes a custom HEAD tag
   * @param int $index 
   * 
   * @return dmWebResponse instance
   */
  
  public function removeCustomHeadTag($index) {
      if (isset($this->customHeadTags[$index])) unset($this->customHeadTags[$index]);
      return $this;
  }
  
  /**
   * Removes all custom HEAD tags by name
   * @param string $tagName 
   * 
   * @return dmWebResponse instance
   */
  public function removeCustomHeadTagsByName($tagName) {
      $bucket = array();
      for ($i=0; $i<count($this->customHeadTags); $i++) {
          if ($this->customHeadTags[$i]['name'] == $tagName) $bucket[] = $i;
      }
      foreach ($bucket as $rem) unset ($this->customHeadTags[$rem]);
      return $this;
  }
  
  /**
   * Removes all custom HEAD tags
   * @return dmWebResponse instance
   */
  public function clearCustomHeadTags() {
      $this->customHeadTags = array();
      return $this;
  }
  
  /**
   * Returns a custom HEAD tag by index
   * @param int $index
   * @return array 
   */
  public function getCustomHeadTagByIndex($index) {
      if (isset($this->customHeadTags[$index]))          return $this->customHeadTags[$index];
      else          return null;
  }
  
  /**
   * Returns all custom HEAD tags
   * @return array
   */
  public function getCustomHeadTags(){
      return $this->customHeadTags;
  }
  
}