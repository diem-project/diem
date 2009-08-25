<?php

class dmOoHelper
{

	protected
	$dmContext;

	public function __construct(dmContext $dmContext)
	{
		$this->dmContext = $dmContext;
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
    $view = new $class($this->dmContext->getSfContext(), $moduleName, $actionName, '');
    $view->setPartialVars($vars);

    return $view->render();
  }

  public function renderComponent($moduleName, $componentName, $vars = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
    return get_component($moduleName, $componentName, $vars);
  }
}