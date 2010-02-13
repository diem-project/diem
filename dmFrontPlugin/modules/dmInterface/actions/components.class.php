<?php

include_once(dmOs::join(sfConfig::get('dm_core_dir'), 'modules/dmInterface/lib/BasedmInterfaceComponents.php'));

class dmInterfaceComponents extends BasedmInterfaceComponents
{

  public function executeToolBar()
  {
    if ($this->getI18n()->hasManyCultures())
    {
      $cultures = array();
      $languages = sfCultureInfo::getInstance($this->getUser()->getCulture())->getLanguages();
      
      foreach($this->getI18n()->getCultures() as $key)
      {
        $cultures[$key] = dmArray::get($languages, $key, $key);
      }
      
      $this->cultureSelect = new sfWidgetFormSelect(array('choices' => $cultures));
    }
    
    if ($this->getService('theme_manager')->getNbThemesEnabled() > 1)
    {
      $this->themeSelect = new sfWidgetFormSelect(array('choices' => $this->getService('theme_manager')->getThemesEnabled()));
    }
  }

}