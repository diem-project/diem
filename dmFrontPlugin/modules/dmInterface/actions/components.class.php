<?php

class dmInterfaceComponents extends dmFrontBaseComponents
{

  public function executeToolBar()
  {
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
    
    $this->themeSelect = new sfWidgetFormSelect(array('choices' => $this->dmContext->getServiceContainer()->getService('theme_manager')->getThemes()));

    if ($this->getUser()->can('widget_add'))
    {
      $this->addMenu = new dmHtmlMenu($this->addMenu());
    }
  }

  protected function addMenu()
  {
    $menu = array();
    
    if($this->getUser()->can('zone_add'))
    {
      $menu[] =array(
        'name' => 'Zone',
        'menu' => array(
          array(
            'name' => 'Zone',
            'class' => 'zone_add move'
          )
        )
      );
    }
    
    foreach($this->dmContext->getService('widget_type_manager')->getWidgetTypes() as $space => $widgetTypes)
    {
      if (empty($widgetTypes))
      {
        continue;
      }
      
      $spaceMenu = array();

      foreach($widgetTypes as $key => $widgetType)
      {
        $spaceMenu[$key] = array(
          'name' => $this->context->getI18n()->__($widgetType->getName()),
          'class' => 'widget_add move',
          'id' => sprintf('dmwa_%s_%s', $widgetType->getModule(), $widgetType->getAction())
        );
      }
      
      $spaceName = $space == 'main'
      ? myConfig::get('site_name')
      : $this->context->getI18n()->__(dmString::humanize(str_replace('dmWidget', '', $space)));

      $menu[$space] = array(
        'name' => $spaceName,
        'menu' => $spaceMenu
      );
    }

    return array(
      array(
        'name' => $this->context->getI18n()->__('Add'),
        'class' => 'strong',
        'menu' => $menu
      )
    );
  }

}