<?php

include_once(dmOs::join(sfConfig::get('dm_core_dir'), 'modules/dmInterface/lib/BasedmInterfaceComponents.php'));

class dmInterfaceComponents extends BasedmInterfaceComponents
{

  public function executeToolBar()
  {
    if ($this->getI18n()->hasManyCultures())
    {
      $cultures = array();

      foreach($this->getI18n()->getCultures() as $key)
      {
        $cultures[$key] = sfCultureInfo::getInstance($key)->getLanguage($key);
      }
    
      $this->cultureSelect = new sfWidgetFormSelect(array('choices' => $cultures));
    }
  }

  public function executeToolBarMenu()
  {
    $this->menu = $this->getService('admin_menu')->build();
  }
}