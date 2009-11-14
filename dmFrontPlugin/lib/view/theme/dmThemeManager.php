<?php

class dmThemeManager
{
  protected
  $dispatcher,
  $serviceContainer,
  $options,
  $themes;
  
  public function __construct(sfEventDispatcher $dispatcher, sfServiceContainer $serviceContainer, array $options)
  {
    $this->dispatcher       = $dispatcher;
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize($options);
  }
  
  public function initialize(array $options)
  {
    $this->options = sfToolkit::arrayDeepMerge(array(
      'list' => array(
        'default_theme' => array(
          'name' => 'Diem default theme',
          'path' => 'dm/front/defaultTheme',
          'enabled' => true
        )
      ),
      'default' => 'default_theme'
    ), $options);
    
    if (!$this->themeKeyExists($this->options['default']))
    {
      $this->options['default'] = dmArray::first($this->getThemeKeys());
    }
    
    $this->themes = array();
  }
  
  public function getConfig()
  {
    return $this->config;
  }
  
  public function getDefaultThemeKey()
  {
    return $this->options['default'];
  }
  
  public function getThemeKeys()
  {
    return array_keys($this->options['list']);
  }
  
  public function themeKeyExists($key)
  {
    return empty($key) ? false : in_array($key, $this->getThemeKeys());
  }
  
  public function getTheme($key)
  {
    if(isset($this->themes[$key]))
    {
      return $this->themes[$key];
    }
    
    if(!isset($this->options['list'][$key]))
    {
      throw new dmException(sprintf('%s is not a valid theme key. These are : %s', $key, implode(', ', $this->getThemeKeys())));
    }
    
    $this->serviceContainer->addParameters(array(
      'theme.options' => array_merge(array('key' => $key), $this->options['list'][$key])
    ));
    
    return $this->themes[$key] = $this->serviceContainer->getService('theme');
  }
  
  public function getThemes()
  {
    foreach($this->getThemeKeys() as $key)
    {
      if (!isset($this->themes[$key]))
      {
        $this->getTheme($key);
      }
    }
    
    return $this->themes;
  }
  
  public function getThemesEnabled()
  {
    $themes = $this->getThemes();
    
    foreach($themes as $key => $theme)
    {
      if (!$theme->isEnabled())
      {
        unset($themes[$key]);
      }
    }
  
    return $themes;
  }
}