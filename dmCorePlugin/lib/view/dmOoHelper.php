<?php

class dmOoHelper
{
	protected
	$context;

	public function __construct(sfContext $context)
	{
		$this->context = $context;
	}

  public function renderPartial($moduleName, $actionName, $vars = array())
  {
  	/*
  	 * partial -> _partial
  	 * dir/partial -> dir/partial
  	 */
    if (!strpos($actionName, '/'))
    {
      $actionName = '_'.$actionName;
    }

    $class = sfConfig::get('mod_'.strtolower($moduleName).'_partial_view_class', 'sf').'PartialView';
    $view = new $class($this->context, $moduleName, $actionName, '');
    $view->setPartialVars($vars);

    return $view->render();
  }

  public function renderComponent($moduleName, $componentName, $vars = array())
  {
    $this->context->getConfiguration()->loadHelpers('Partial');
    
    return get_component($moduleName, $componentName, $vars);
  }
}