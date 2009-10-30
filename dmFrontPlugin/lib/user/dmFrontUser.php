<?php

class dmFrontUser extends dmUser
{
  protected
    $themeManager;
    
  /**
   * The namespace under which theme keys will be stored.
   */
  const THEME_NAMESPACE = 'symfony/user/sfUser/theme';

  public function listenToContextLoadedEvent(sfEvent $e)
  {
    parent::listenToContextLoadedEvent($e);
    
    $this->setThemeManager($e->getSubject()->get('theme_manager'));
    
    $this->getTheme();
  }
  
  public function setThemeManager(dmThemeManager $themeManager)
  {
    $this->themeManager = $themeManager;
  }

  /*
   * @return dmTheme the current user theme
   */
  public function getTheme()
  {
    if($this->hasCache('theme'))
    {
      return $this->getCache('theme');
    }
    
    $themeKey = $this->storage->read(self::THEME_NAMESPACE);
    
    if (!$this->themeManager->themeKeyExists($themeKey))
    {
      $themeKey = $this->themeManager->getDefaultThemeKey();
    }

    return $this->setTheme($themeKey);
  }

  public function setTheme($theme)
  {
    if (is_string($theme))
    {
      $theme = $this->themeManager->getTheme($theme);
    }
    
    if (!$theme instanceof dmTheme)
    {
      throw new dmException(sprintf('%s is not a valid dmTheme', $theme));
    }
    
    if ($theme->getKey() != $this->storage->read(self::THEME_NAMESPACE))
    {
      $this->storage->write(self::THEME_NAMESPACE, $theme->getKey());
    }

    $this->dispatcher->notify(new sfEvent($this, 'user.change_theme', array('theme' => $theme)));
    
    return $this->setCache('theme', $theme);
  }

  public function getIsEditMode()
  {
    return $this->can('zone_add widget_add') && $this->getAttribute('dm_front_edit');
  }

  public function setIsEditMode($value)
  {
    return $this->setAttribute('dm_front_edit', $value);
  }
  
  public function getShowToolBar()
  {
    return $this->can('tool_bar_front ') && $this->getAttribute('dm_front_show_tool_bar', true);
  }
  
  public function setShowToolBar($value)
  {
    return $this->setAttribute('dm_front_show_tool_bar', $value);
  }

}