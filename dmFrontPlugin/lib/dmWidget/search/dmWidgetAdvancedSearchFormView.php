<?php

class dmWidgetAdvancedSearchFormView extends dmWidgetPluginView
{

	public function getRequiredVars()
	{
    return array();
	}

	public function getViewVars(array $vars = array())
  {
    $vars = parent::getViewVars($vars);
    
    $vars['form'] = new mySearchForm();
    
    return $vars;
  }

}