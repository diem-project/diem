<?php

class dmInterfaceComponents extends dmAdminBaseComponents
{

  public function executeToolBar()
  {
    $this->menu = new dmHtmlMenu($this->getDmContext()->getAdminMenu()->load());

    if ($this->context->getI18n()->hasManyCultures())
    {
      $this->cultures = array();
      
      $languages = sfCultureInfo::getInstance($this->getUser()->getCulture())->getLanguages();
      
      foreach($this->context->getI18n()->getCultures() as $key)
      {
        $this->cultures[$key] = dmArray::get($languages, $key, $key);
      }
    }
  }
}