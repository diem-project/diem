<?php

class dmThemeManager extends dmConfigurable
{
  protected
  $dispatcher,
  $serviceContainer,
  $themes;

  public function __construct(sfEventDispatcher $dispatcher, sfServiceContainer $serviceContainer, array $options)
  {
    $this->dispatcher       = $dispatcher;
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize($options);
  }

  protected function initialize(array $options)
  {
    $this->configure($options);

    if (!$this->themeNameExists($this->options['default']))
    {
      $this->options['default'] = dmArray::first($this->getThemeNames());
    }
    
    $this->themes = array();
  }

  public function configure(array $options = array())
  {
    $options['default'] = null;
    
    foreach($options['list'] as $themeName => $themeConfig)
    {
      // enabled options defaults to true
      if(null === dmArray::get($themeConfig, 'enabled'))
      {
        $options['list'][$themeName]['enabled'] = true;
      }

      // first enabled theme is the default theme
      if(null === $options['default'] && $options['list'][$themeName]['enabled'])
      {
        $options['default'] = $themeName;
      }

      // path is renamed to dir BC 5.0_BETA6
      if(isset($options['list'][$themeName]['path']))
      {
        $options['list'][$themeName]['dir'] = $options['list'][$themeName]['path'];
        unset($options['list'][$themeName]['path']);
      }

      // theme key is the theme name
      $options['list'][$themeName]['name'] = $themeName;
    }

    if(null === $options['default'])
    {
      throw new dmException('No theme is enabled!');
    }

    return parent::configure($options);
  }

  public function getDefaultThemeName()
  {
    return $this->options['default'];
  }

  public function getDefaultTheme()
  {
    return $this->getTheme($this->getDefaultThemeName());
  }

  public function getThemeNames()
  {
    return array_keys($this->options['list']);
  }

  public function themeNameExists($name)
  {
    return empty($name) ? false : in_array($name, $this->getThemeNames());
  }

  public function getTheme($name)
  {
    if(isset($this->themes[$name]))
    {
      return $this->themes[$name];
    }
    
    if(!isset($this->options['list'][$name]))
    {
      throw new dmException(sprintf('%s is not a valid theme name. These are : %s', $name, implode(', ', $this->getThemeNames())));
    }

    $this->serviceContainer->setParameter('theme.options', $this->options['list'][$name]);
    
    return $this->themes[$name] = $this->serviceContainer->getService('theme');
  }

  public function getThemes()
  {
    foreach($this->getThemeNames() as $name)
    {
      if (!isset($this->themes[$name]))
      {
        $this->getTheme($name);
      }
    }
    
    return $this->themes;
  }
  
  public function getNbThemesEnabled()
  {
    return count($this->getThemesEnabled());
  }
  
  public function getThemesEnabled()
  {
    $themes = $this->getThemes();
    
    foreach($themes as $name => $theme)
    {
      if (!$theme->isEnabled())
      {
        unset($themes[$name]);
      }
    }
  
    return $themes;
  }
}