<?php

class dmFrontAddMenu extends dmMenu
{

  public function build()
  {
    $this
    ->name('Front add menu')
    ->ulClass('ui-helper-reset level0')
    ->addChild('Add')
    ->setOption('root_add', true)
    ->ulClass('ui-widget ui-widget-content level1')
    ->liClass('ui-corner-bottom ui-state-default')
    ->addClipboard()
    ->addWidgets();
    
    $this->serviceContainer->getService('dispatcher')->notify(new sfEvent($this, 'dm.front.add_menu', array()));

    return $this;
  }

  public function addClipboard()
  {
    if($widget = $this->serviceContainer->getService('front_clipboard')->getWidget())
    {
      $this
      ->addChild('Clipboard')->credentials('widget_add')->ulClass('clearfix level2')->liClass('dm_droppable_widgets')
      ->addChild($this->serviceContainer->get('widget_type_manager')->getWidgetType($widget)->getName())
      ->setOption('clipboard_widget', $widget)
      ->setOption('clipboard_method', $this->serviceContainer->getService('front_clipboard')->getMethod());
    }

    return $this;
  }
  
  public function addWidgets()
  {
    $moduleManager = $this->serviceContainer->getService('module_manager');
    
    foreach($this->serviceContainer->get('widget_type_manager')->getWidgetTypes() as $space => $widgetTypes)
    {
      $spaceName = ($module = $moduleManager->getModuleOrNull($space))
      ? $module->getName()
      : dmString::humanize(str_replace('dmWidget', '', $space));
      
      $spaceMenu = $this->addChild($space)
      ->label($this->getI18n()->__($spaceName))
      ->ulClass('clearfix level2')
      ->liClass('dm_droppable_widgets');
      
      foreach($widgetTypes as $key => $widgetType)
      {
        $spaceMenu
        ->addChild($widgetType->getName())
        ->label($this->getI18n()->__($widgetType->getName()))
        ->setOption('widget_type', $widgetType);
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
        dmString::strtolower(parent::renderLabel())
      );
    }
    elseif($widget = $this->getOption('clipboard_widget'))
    {
      return sprintf('<span class="widget_paste move dm_%s" id="dmwp_%d">%s</span>',
        $this->getOption('clipboard_method'),
        $widget->get('id'),
        dmString::strtolower(parent::renderLabel())
      );
    }
    elseif($this->getOption('root_add'))
    {
      return '<a class="tipable s24block s24_add widget24" title="'.$this->__('Add widgets').'"></a>';
    }
    
    return '<a>'.dmString::strtolower(parent::renderLabel()).'</a>';
  }
}