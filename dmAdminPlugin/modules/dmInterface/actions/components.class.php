<?php

class dmInterfaceComponents extends dmAdminBaseComponents
{

  public function executeToolBar()
  {
    $this->menu = new dmHtmlMenu($this->dmContext->getService('admin_menu')->load());

    if ($this->context->getI18n()->hasManyCultures())
    {
      $cultures = array();
      $languages = sfCultureInfo::getInstance($this->getUser()->getCulture())->getLanguages();
      
      foreach($this->context->getI18n()->getCultures() as $key)
      {
        $cultures[$key] = dmArray::get($languages, $key, $key);
      }
    
      $this->cultureSelect = new sfWidgetFormSelect(array('choices' => $cultures));
    }
  }
}