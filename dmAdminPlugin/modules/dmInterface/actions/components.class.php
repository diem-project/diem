<?php

include_once(dmOs::join(sfConfig::get('dm_core_dir'), 'modules/dmInterface/lib/BasedmInterfaceComponents.php'));

class dmInterfaceComponents extends BasedmInterfaceComponents
{

  public function executeToolBar()
  {
    $this->menu = $this->getService('admin_menu')->build();

    if ($this->getI18n()->hasManyCultures())
    {
      $cultures = array();

      foreach($this->getI18n()->getCultures() as $key)
      {
        try
        {
          $cultures[$key] = sfCultureInfo::getInstance($key)->getLanguage($key);
        }
        catch(sfException $e)
        {
          $cultures[$key] = $key;
        }
      }
    
      $this->cultureSelect = new sfWidgetFormSelect(array('choices' => $cultures));
    }
  }
}