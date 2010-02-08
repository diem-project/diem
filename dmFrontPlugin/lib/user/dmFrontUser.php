<?php

class dmFrontUser extends dmCoreUser
{
  protected
    $themeManager;
    
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
    
    $themeName = $this->getAttribute('dm_theme');
    
    if (!$this->themeManager->themeNameExists($themeName))
    {
      $themeName = $this->themeManager->getDefaultThemeName();
    }

    return $this->setTheme($themeName);
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
    
    if ($theme->getName() != $this->getAttribute('dm_theme'))
    {
      $this->setAttribute('dm_theme', $theme->getName());
    }

    $this->dispatcher->notify(new sfEvent($this, 'user.change_theme', array('theme' => $theme)));
    
    return $this->setCache('theme', $theme);
  }

  public function getIsEditMode()
  {
    return $this->can('zone_add, widget_add') && $this->getAttribute('dm_front_edit');
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