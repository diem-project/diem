<?php

class dmFrontAddMenu extends dmMenu
{

  public function build()
  {
    $this
    ->name('Front add menu')
    ->ulClass('ui-helper-reset level0')
    ->addChild('Add')
    ->ulClass('ui-widget ui-widget-content level1')
    ->liClass('ui-corner-bottom ui-state-default')
    ->addClipboard()
    ->addZones()
    ->addWidgets();
    
    $this->serviceContainer->getService('dispatcher')->notify(new sfEvent($this, 'dm.front.add_menu', array()));

    return $this;
  }

  public function addClipboard()
  {
    if($widget = $this->serviceContainer->getService('front_clipboard')->getWidget())
    {
      $this
      ->addChild('Clipboard')->credentials('widget_add')->ulClass('clearfix level2')
      ->addChild($this->serviceContainer->get('widget_type_manager')->getWidgetType($widget)->getName())
      ->setOption('clipboard_widget', $widget)
      ->setOption('clipboard_method', $this->serviceContainer->getService('front_clipboard')->getMethod());
    }

    return $this;
  }

  public function addZones()
  {
    return $this
    ->addChild('Zone')->credentials('zone_add')->ulClass('clearfix level2')
    ->addChild('Zone')->setOption('is_zone', true)
    ->end()->end();
  }
  
  public function addWidgets()
  {
    foreach($this->serviceContainer->get('widget_type_manager')->getWidgetTypes() as $space => $widgetTypes)
    {
      $spaceMenu = $this->addChild(
      ($module = $this->serviceContainer->getService('module_manager')->getModuleOrNull($space))
      ? $module->getName()
      : dmString::humanize(str_replace('dmWidget', '', $space))
      )
      ->ulClass('clearfix level2');
      
      foreach($widgetTypes as $key => $widgetType)
      {
        $spaceMenu->addChild($widgetType->getName())->setOption('widget_type', $widgetType);
      }

      if(!$spaceMenu->hasChildren())
      {
        $this->removeChild($spaceMenu);
      }
    }
    
    return $this;
  }

  public function renderLabel()
  {
    if($widgetType = $this->getOption('widget_type'))
    {
      return sprintf('<span class="widget_add move" id="dmwa_%s-%s">%s</span>',
        $widgetType->getModule(),
        $widgetType->getAction(),
        parent::renderLabel()
      );
    }
    elseif($this->getOption('is_zone'))
    {
      return '<span class="zone_add move">'.parent::renderLabel().'</a>';
    }
    elseif($widget = $this->getOption('clipboard_widget'))
    {
      return sprintf('<span class="widget_paste move dm_%s" id="dmwp_%d">%s</span>',
        $this->getOption('clipboard_method'),
        $widget->get('id'),
        parent::renderLabel()
      );
    }
    
    return '<a>'.parent::renderLabel().'</a>';
  }
}