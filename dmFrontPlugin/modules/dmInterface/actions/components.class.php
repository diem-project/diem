<?php

include_once(dmOs::join(sfConfig::get('dm_core_dir'), 'modules/dmInterface/lib/BasedmInterfaceComponents.php'));

class dmInterfaceComponents extends BasedmInterfaceComponents
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
    
    $themeManager = $this->context->getServiceContainer()->getService('theme_manager');
    
    if ($themeManager->getNbThemesEnabled() > 1)
    {
      $this->themeSelect = new sfWidgetFormSelect(array('choices' => $themeManager->getThemesEnabled()));
    }
    
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
    
    foreach($this->context->get('widget_type_manager')->getWidgetTypes() as $space => $widgetTypes)
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
          'id' => sprintf('dmwa_%s-%s', $widgetType->getModule(), $widgetType->getAction())
        );
      }
      
      $spaceName = $space == 'main'
      ? dmConfig::get('site_name')
      : ( ($module = $this->context->getModuleManager()->getModuleOrNull($space))
        ? $this->context->getI18n()->__($module->getName())
        : $this->context->getI18n()->__(dmString::humanize(str_replace('dmWidget', '', $space)))
      );

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