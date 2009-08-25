<?php

class BasedmFrontComponents extends dmFrontBaseComponents
{

  public function executeToolBar()
  {
  	$this->cultures = array();
    $languages = sfCultureInfo::getInstance(dm::getUser()->getCulture())->getLanguages();
  	foreach(dm::getI18n()->getCultures() as $key)
  	{
  		$this->cultures[$key] = dmArray::get($languages, $key, $key);
  	}

  	$this->themes = dmTheme::getList();
  }

}