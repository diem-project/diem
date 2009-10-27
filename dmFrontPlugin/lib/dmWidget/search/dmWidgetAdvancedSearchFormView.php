<?php

class dmWidgetAdvancedSearchFormView extends dmWidgetPluginView
{
  protected
  $isIndexable = false;

  public function getRequiredVars()
  {
    return array();
  }

  protected function filterViewVars(array $vars = array())
  {
    $vars = parent::getViewVars($vars);
    
    $vars['form'] = new mySearchForm;
    
    return $vars;
  }

}