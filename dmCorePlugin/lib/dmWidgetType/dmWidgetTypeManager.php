<?php

class dmWidgetTypeManager extends dmConfigurable
{
  protected
  $dispatcher,
  $serviceContainer,
  $widgetTypes;

  public function __construct(sfEventDispatcher $dispatcher, dmBaseServiceContainer $serviceContainer, array $options = array())
  {
    $this->dispatcher       = $dispatcher;
    $this->serviceContainer = $serviceContainer;

    $this->initialize($options);
  }

  public function getDefaultOptions()
  {
    return array(
      'config_file' => 'config/dm/widget_types.yml'
    );
  }
  
  public function initialize(array $options = array())
  {
    $this->configure($options);
    
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
        $internalConfigFile = $this->serviceContainer->getService('config_cache')->checkConfig($this->getOption('config_file'));
        $internalConfig = include($internalConfigFile);

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
              'public_name' => dmArray::get($action, 'public_name', dmString::humanize($name)),
              'form_class' => dmArray::get($action, 'form_class', $fullKey.'Form'),
              'view_class' => dmArray::get($action, 'view_class', $fullKey.'View'),
              'use_component' => dmArray::get($action, 'use_component', false) || $this->componentExists($moduleKey, $fullKey),
              'cache'      => dmArray::get($action, 'cache', false),
              'assets'     => dmArray::get($action, 'assets', array())
            );

            $this->widgetTypes[$moduleKey][$actionKey] = new dmWidgetType($moduleKey, $actionKey, $widgetTypeConfig);
          }
        }

        foreach($this->serviceContainer->getService('module_manager')->getProjectModules() as $moduleKey => $module)
        {
          $this->widgetTypes[$moduleKey] = array();

          foreach($module->getComponents() as $componentKey => $component)
          {
            $baseClass = 'dmWidget'.dmString::camelize($component->getType());

            $widgetTypeConfig = array(
              'full_key'   => $moduleKey.ucfirst($componentKey),
              'name'       => $component->getName(),
              'public_name' => $module->getName().' '.dmString::humanize($component->getName()),
              'form_class' => $component->getFormClass(),
              'view_class' => $component->getViewClass(),
              'use_component' => dmArray::get($component, 'use_component', false) || $this->componentExists($moduleKey, $componentKey),
              'cache'      => $component->isCachable(),
              'assets'     => dmArray::get($action, 'assets', array())
            );
                    
            $this->widgetTypes[$moduleKey][$componentKey] = new dmWidgetType($moduleKey, $componentKey, $widgetTypeConfig);
          }
        }
      
        $cache->set('types', $this->widgetTypes);
      }
    }
    
    return $this->widgetTypes;
  }

  protected function componentExists($module, $action)
  {
    if ('front' !== sfConfig::get('sf_app'))
    {
      return false;
    }

    try
    {
      return $this->serviceContainer->getService('controller')->componentExists($module, $action);
    }
    catch(sfConfigurationException $e)
    {
      return false;
    }
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

      throw new dmException(sprintf("The %s/%s widget does not exist", $module, $action));
    }

    return $widgetType;
  }

  public function getWidgetTypeOrNull($moduleOrWidget, $action = null)
  {
    return $this->getWidgetType($moduleOrWidget, $action, true);
  }

}