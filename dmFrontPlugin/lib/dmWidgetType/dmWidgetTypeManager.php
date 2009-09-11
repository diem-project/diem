<?php

class dmWidgetTypeManager
{

  protected
  $dispatcher,
  $cacheManager,
  $context,
  $configFile,
  $widgetTypes;

  public function construct(sfEventDispatcher $dispatcher, dmCacheManager $cacheManager, sfContext $context, array $options = array())
  {
    $this->dispatcher   = $dispatcher;
    $this->cacheManager = $cacheManager;
    $this->context      = $context;
    
    $this->initialize($options);
  }
  
  public function initialize(array $options = array())
  {
    $this->configFile = dmArray::get($options, 'config_files', 'config/dm/widget_types.yml');
    
    $this->widgetTypes = null;
  }
  
  public function getWidgetTypes()
  {
  	$timer = dmDebug::timer('dmWidgetTypeManager::getWidgetTypes');
  	
    if (is_null($this->widgetTypes))
    {
    	if (!$this->widgetTypes = $this->cacheManager->getCache('dm/widget')->get('types'))
    	{
	    	$internalConfig = include($this->context->getConfigCache()->checkConfig($this->configFile));

	    	$sfController = $this->context->getController();

	    	$this->widgetTypes = array();

	      foreach($internalConfig as $moduleKey => $actions)
	      {
	        $this->widgetTypes[$moduleKey] = array();

	        foreach($actions as $actionKey => $action)
	        {
	        	$fullKey = $moduleKey.dmString::camelize($actionKey);

	        	$widgetTypeConfig = array(
	        	  'name'       => dmArray::get($action, 'name'),
	            'form_class' => dmArray::get($action, 'form_class', $fullKey.'Form'),
	            'view_class' => dmArray::get($action, 'view_class', $fullKey.'View'),
	        	  'use_component' => $sfController->componentExists('dmWidget', $fullKey)
		        );

	        	$this->widgetTypes[$moduleKey][$actionKey] = new dmWidgetType($moduleKey, $actionKey, $widgetTypeConfig);
	        }
	      }

	      foreach(dmModuleManager::getProjectModules() as $moduleKey => $module)
	      {
	      	$moduleName = $module->getName();

	        $this->widgetTypes[$moduleKey] = array();

	        foreach($module->getActions() as $actionKey => $action)
	        {
	        	$baseClass = 'dmWidget'.dmString::camelize($action->getType());

	        	$widgetTypeConfig = array(
	        	  'name'   => $action->getName(),
	        	  'form_class' => $baseClass.'Form',
	            'view_class' => $baseClass.'View',
	            'use_component' => $sfController->componentExists($moduleKey, $actionKey)
	        	);

	          $this->widgetTypes[$moduleKey][$actionKey] = new dmWidgetType($moduleKey, $actionKey, $widgetTypeConfig);
	        }
	      }
    	}
    	$dmContext->getCacheManager()->getCache('dm/widget')->set('types', $this->widgetTypes);
    }

    $timer->addTime();
    return $this->widgetTypes;
  }

  public function getWidgetType($moduleOrWidget, $action = null, $orNull = false)
  {
  	if ($moduleOrWidget instanceof DmWidget)
  	{
  		list($module, $action) = array($moduleOrWidget->get('module'), $moduleOrWidget->get('action'));
  	}
  	else
  	{
  		$module = $moduleOrWidget;
  	}

    $widgetType = dmArray::get(dmArray::get($this->getWidgetTypes(), dmString::modulize($module), array()), dmString::modulize($action));

    if (!$widgetType)
    {
      if ($orNull)
      {
        return null;
      }
//      dmDebug::stack();
//      dmDebug::kill($this->getWidgetTypes());
      throw new dmException(sprintf("The %s.%s module does not exist", $module, $action));
    }

    return $widgetType;
  }

  public function getWidgetTypeOrNull($moduleOrWidget, $action = null)
  {
  	return $this->getWidgetType($moduleOrWidget, $action, true);
  }

}