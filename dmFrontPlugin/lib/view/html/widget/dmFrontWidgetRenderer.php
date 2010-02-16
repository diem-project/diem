<?php

class dmFrontWidgetRenderer
{
  protected
  $dispatcher,
  $serviceContainer,
  $widget,
  $widgetView,
  $isRendered,
  $html,
  $stylesheets,
  $javascripts;
  
  public function __construct(sfEventDispatcher $dispatcher, dmFrontBaseServiceContainer $serviceContainer, $widget)
  {
    $this->dispatcher       = $dispatcher;
    $this->serviceContainer = $serviceContainer;
    
    if ($widget instanceof DmWidget)
    {
      $this->widget = $widget->toArray();
    }
    elseif(is_array($widget))
    {
      $this->widget = $widget;
    }
    else
    {
      throw new dmException('the widget parameter must be a DmWidget instance or an array');
    }
    
    $this->initialize();
  }
  
  protected function initialize()
  {
    $this->isRendered = false;
  }
  
  public function getHtml()
  {
    $this->doRender();
    
    return $this->html;
  }
  
  public function getStylesheets()
  {
    $this->doRender();
    
    return $this->stylesheets;
  }
  
  public function getJavascripts()
  {
    $this->doRender();
    
    return $this->javascripts;
  }
  
  protected function doRender()
  {
    if ($this->isRendered)
    {
      return;
    }

    $widgetView = $this->getWidgetView();
    
    $this->html        = $widgetView->render();
    $this->stylesheets = $widgetView->getStylesheets();
    $this->javascripts = $widgetView->getJavascripts();

    $this->isRendered = true;
  }
  
  public function getWidgetView()
  {
    if (null === $this->widgetView)
    {
      $widgetType = $this->serviceContainer->get('widget_type_manager')->getWidgetType($this->widget['module'], $this->widget['action']);
      
      $this->serviceContainer->addParameters(array(
        'widget_view.class' => $widgetType->getViewClass(),
        'widget_view.type'  => $widgetType,
        'widget_view.data'  => $this->widget
      ));
      
      $this->widgetView = $this->serviceContainer->getService('widget_view');
    }
    
    return $this->widgetView;
  }
}