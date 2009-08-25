<?php

class dmFrontWebResponse extends dmWebResponse
{
  protected
  $preserveThemeStylesheets,
  $themeCssWebPath;
  
  protected function getCachedStylesheets()
  {
  	if($this->preserveThemeStylesheets = dm::getUser()->can('code_editor'))
  	{
      $this->themeCssWebPath = dm::getUser()->getTheme()->getPath('css');
  	}
  	
  	return parent::getCachedStylesheets();
  }
  
  protected function isStylesheetCachable($stylesheet)
  {
  	if (!$this->preserveThemeStylesheets)
  	{
  		return true;
  	}
  	
    return strpos($stylesheet, $this->themeCssWebPath) !== 0;
  }
}