<?php

include_once(dmOs::join(sfConfig::get('dm_core_dir'), 'modules/dmInterface/lib/BasedmInterfaceComponents.php'));

class dmInterfaceComponents extends BasedmInterfaceComponents
{

  public function executeToolBar()
  {
    $this->menu = $this->context->get('admin_menu')->build();

    if ($this->context->getI18n()->hasManyCultures())
    {
      $cultures = array();

      foreach($this->context->getI18n()->getCultures() as $key)
      {
        $cultures[$key] = sfCultureInfo::getInstance($key)->getLanguage($key);
      }
    
      $this->cultureSelect = new sfWidgetFormSelect(array('choices' => $cultures));
    }
  }
}