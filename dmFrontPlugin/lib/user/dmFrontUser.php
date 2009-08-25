<?php

class dmFrontUser extends dmUser
{

  /*
   * Theme methods
   */
  public function getTheme()
  {
    if($this->hasCache('theme'))
    {
      return $this->getCache('theme');
    }

    if (!$theme = dmTheme::getTheme($this->getThemeKey()))
    {
    	$defaultThemeKey = 'dm/front/themeDefault';
    	$theme = dmTheme::getTheme($defaultThemeKey);
      $this->setAttribute('dm.theme.current', $defaultThemeKey);
    }

    return $this->setCache('theme', $theme);
  }

  public function getThemeKey()
  {
    if (!$currentThemeKey = $this->getAttribute('dm.theme.current'))
    {
      $currentThemeKey = $this->setThemeKey(sfConfig::get('dm_theme_default', 'dm/themeDefault'));
    }

    return $currentThemeKey;
  }

  public function setThemeKey($themeKey)
  {
    $this->setAttribute('dm.theme.current', $themeKey);
    $this->clearCache('theme');
    return $themeKey;
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