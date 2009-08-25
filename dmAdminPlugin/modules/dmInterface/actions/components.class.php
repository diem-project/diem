<?php

class dmInterfaceComponents extends dmAdminBaseComponents
{

  public function executeToolBar()
  {
    $adminMenuStructure = new myAdminMenu();

    $this->menu = new dmHtmlMenu($adminMenuStructure->getMenu());

    $this->cultures = array();
    $languages = sfCultureInfo::getInstance(dm::getUser()->getCulture())->getLanguages();
    foreach(dm::getI18n()->getCultures() as $key)
    {
      $this->cultures[$key] = dmArray::get($languages, $key, $key);
    }
  }
}