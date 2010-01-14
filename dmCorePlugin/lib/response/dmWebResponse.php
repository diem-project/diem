<?php

abstract class dmWebResponse extends sfWebResponse
{
  protected
  $isHtmlForHuman = true,
  $assetAliases,
  $cdnConfig,
  $javascriptConfig,
  $culture,
  $theme;
  
  public function initialize(sfEventDispatcher $dispatcher, $options = array())
  {
    $options['with_google_maps'] = false;
    
    parent::initialize($dispatcher, $options);
    
    $this->javascriptConfig = array();
    
    $this->dispatcher->connect('user.change_culture', array($this, 'listenToChangeCultureEvent'));
    
    $this->dispatcher->connect('user.change_theme', array($this, 'listenToChangeThemeEvent'));
    
    $this->dispatcher->connect('user.remember_me', array($this, 'listenToRememberMeEvent'));
    
    $this->dispatcher->connect('user.sign_out', array($this, 'listenToSignOutEvent'));
    
    $this->dispatcher->connect('dm.layout.filter_javascripts', array($this, 'listenToFilterJavascriptsEvent'));
  }
  
  /*
   * Add the google maps api to the response
   */
  public function withGoogleMaps($val = true)
  {
    $this->options['with_google_maps'] = (bool) $val;
  }
  
  public function listenToFilterJavascriptsEvent(sfEvent $event, array $assets)
  {
    if ($this->options['with_google_maps'])
    {
      if (!($key = dmConfig::get('gmap_key')) && $this->options['logging'])
      {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array('priority' => sfLogger::ERR, 'dmResponse can not load google maps api. You must provide an api key in the configuration panel')));
      }
      else
      {
        $config = dmArray::get(sfConfig::get('dm_js_api'), 'gmap', array('version' => 2, 'sensor' => false, 'url' => 'http://maps.google.com/maps?file=api&v=%version%&sensor=%sensor%&key=%key%'));
        
        $assets[strtr($config['url'], array(
          '%version%' => $config['version'],
          '%sensor%'  => $config['sensor'] ? 'true' : 'false',
          '%key%'     => $key
        ))] = array();
      }
    }
    
    return $assets;
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
        $path = $this->theme->getPath('css/'.$asset.'.css');
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
  
  /*
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
}