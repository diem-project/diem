<?php

class dmWidgetTypeManager
{

  protected
  $dispatcher,
  $serviceContainer,
  $options,
  $widgetTypes;

  public function __construct(sfEventDispatcher $dispatcher, dmBaseServiceContainer $serviceContainer, array $options = array())
  {
    $this->dispatcher       = $dispatcher;
    $this->serviceContainer = $serviceContainer;

    $this->initialize($options);
  }
  
  public function initialize(array $options = array())
  {
    $this->options = array_merge(array(
      'config_file' => 'config/dm/widget_types.yml'
    ), $options);
    
    $this->widgetTypes = null;
  }
  
  public function getWidgetTypes()
  {
    if (null === $this->widgetTypes)
    {
      $cache = $this->serviceContainer
      ->getService('cache_manager')
      ->getCache('dm/widget/'.sfConfig::get('sf_app').sfConfig::get('sf_environment'));
      
      $this->widgetTypes = $cache->get('types');
      
      if (empty($this->widgetTypes))
      {
        $internalConfig = include($this->serviceContainer->getService('config_cache')->checkConfig($this->options['config_file']));

        $this->widgetTypes = array();
        
        $controller = $this->serviceContainer->getService('controller');

        foreach($internalConfig as $moduleKey => $actions)
        {
          $this->widgetTypes[$moduleKey] = array();

          foreach($actions as $actionKey => $action)
          {
            $fullKey = $moduleKey.dmString::camelize($actionKey);
            $name    = dmArray::get($action, 'name', dmString::humanize($actionKey));

            $widgetTypeConfig = array(
              'full_key'   => $moduleKey.ucfirst($actionKey),
              'name'       => $name,
              'public_name' => dmString::humanize($name),
              'form_class' => dmArray::get($action, 'form_class', $fullKey.'Form'),
              'view_class' => dmArray::get($action, 'view_class', $fullKey.'View'),
              'use_component' => $controller->componentExists($moduleKey, $fullKey),
              'cache'      => dmArray::get($action, 'cache', false)
            );

            $this->widgetTypes[$moduleKey][$actionKey] = new dmWidgetType($moduleKey, $actionKey, $widgetTypeConfig);
          }
        }

        foreach($this->serviceContainer->getService('module_manager')->getProjectModules() as $moduleKey => $module)
        {
          $this->widgetTypes[$moduleKey] = array();

          foreach($module->getActions() as $actionKey => $action)
          {
            $baseClass = 'dmWidget'.dmString::camelize($action->getType());

            $widgetTypeConfig = array(
              'full_key'   => $moduleKey.ucfirst($actionKey),
              'name'       => $action->getName(),
              'public_name' => $module->getName().' '.dmString::humanize($action->getName()),
              'form_class' => $baseClass.'Form',
              'view_class' => $baseClass.'View',
              'use_component' => $controller->componentExists($moduleKey, $actionKey),
              'cache'      => $action->isCachable()
            );
            
            $this->widgetTypes[$moduleKey][$actionKey] = new dmWidgetType($moduleKey, $actionKey, $widgetTypeConfig);
          }
        }
      
        $cache->set('types', $this->widgetTypes);
      }
    }
        
    return $this->widgetTypes;
  }

  public function getWidgetType($moduleOrWidget, $action = null, $orNull = false)
  {
    if (is_array($moduleOrWidget) || $moduleOrWidget instanceof DmWidget)
    {
      list($module, $action) = array($moduleOrWidget['module'], $moduleOrWidget['action']);
    }
    else
    {
      $module = $moduleOrWidget;
    }

    $widgetType = dmArray::get(dmArray::get($this->getWidgetTypes(), $module, array()), $action);

    if (!$widgetType)
    {
      if ($orNull)
      {
        return null;
      }

      throw new dmException(sprintf("The %s.%s widget type does not exist", $module, $action));
    }

    return $widgetType;
  }

  public function getWidgetTypeOrNull($moduleOrWidget, $action = null)
  {
    return $this->getWidgetType($moduleOrWidget, $action, true);
  }

}