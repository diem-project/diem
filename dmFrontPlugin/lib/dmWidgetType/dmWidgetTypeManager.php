<?php

class dmWidgetTypeManager
{

  protected static
  $configFile = 'config/dm/widget_types.yml',
  $widgetTypes;

  public static function getWidgetTypes()
  {
  	$timer = dmDebug::timer('dmWidgetTypeManager::getWidgetTypes');
    if (is_null(self::$widgetTypes))
    {
    	if (!self::$widgetTypes = dmCacheManager::getCache('dm/widget')->get('types'))
    	{
	    	$internalConfig = include(sfContext::getInstance()->getConfigCache()->checkConfig(self::$configFile));

	    	$sfController = sfContext::getInstance()->getController();

	    	self::$widgetTypes = array();

	      foreach($internalConfig as $moduleKey => $actions)
	      {
	        self::$widgetTypes[$moduleKey] = array();

	        foreach($actions as $actionKey => $action)
	        {
	        	$fullKey = $moduleKey.dmString::camelize($actionKey);

	        	$widgetTypeConfig = array(
	        	  'name'       => dmArray::get($action, 'name'),
	            'form_class' => dmArray::get($action, 'form_class', $fullKey.'Form'),
	            'view_class' => dmArray::get($action, 'view_class', $fullKey.'View'),
	        	  'use_component' => $sfController->componentExists('dmWidget', $fullKey)
		        );

	        	self::$widgetTypes[$moduleKey][$actionKey] = new dmWidgetType($moduleKey, $actionKey, $widgetTypeConfig);
	        }
	      }

	      foreach(dmModuleManager::getProjectModules() as $moduleKey => $module)
	      {
	      	$moduleName = $module->getName();

	        self::$widgetTypes[$moduleKey] = array();

	        foreach($module->getActions() as $actionKey => $action)
	        {
	        	$baseClass = 'dmWidget'.dmString::camelize($action->getType());

	        	$widgetTypeConfig = array(
	        	  'name'   => $action->getName(),
	        	  'form_class' => $baseClass.'Form',
	            'view_class' => $baseClass.'View',
	            'use_component' => $sfController->componentExists($moduleKey, $actionKey)
	        	);

	          self::$widgetTypes[$moduleKey][$actionKey] = new dmWidgetType($moduleKey, $actionKey, $widgetTypeConfig);
	        }
	      }
    	}
    	dmCacheManager::getCache('dm/widget')->set('types', self::$widgetTypes);
    }

    $timer->addTime();
    return self::$widgetTypes;
  }

  public static function getWidgetType($moduleOrWidget, $action = null, $orNull = false)
  {
  	if ($moduleOrWidget instanceof DmWidget)
  	{
  		list($module, $action) = array($moduleOrWidget->get('module'), $moduleOrWidget->get('action'));
  	}
  	else
  	{
  		$module = $moduleOrWidget;
  	}

    $widgetType = dmArray::get(dmArray::get(self::getWidgetTypes(), dmString::modulize($module), array()), dmString::modulize($action));

    if (!$widgetType)
    {
      if ($orNull)
      {
        return null;
      }
//      dmDebug::stack();
//      dmDebug::kill(self::getWidgetTypes());
      throw new dmException(sprintf("The %s.%s module does not exist", $module, $action));
    }

    return $widgetType;
  }

  public static function getWidgetTypeOrNull($moduleOrWidget, $action = null)
  {
  	return self::getWidgetType($moduleOrWidget, $action, true);
  }

}