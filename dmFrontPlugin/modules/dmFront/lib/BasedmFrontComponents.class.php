<?php

class BasedmFrontComponents extends dmFrontBaseComponents
{

  public function executeToolBar()
  {
    $this->cultures = array();
    $languages = sfCultureInfo::getInstance($this->getUser()->getCulture())->getLanguages();
    foreach($this->context->getI18n()->getCultures() as $key)
    {
      $this->cultures[$key] = dmArray::get($languages, $key, $key);
    }

    $this->themes = dmTheme::getList();
  }

}