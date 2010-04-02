<?php

class dmWidgetSearchFormView extends dmWidgetPluginView
{
  protected
  $isIndexable = false;

  protected function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);
    
    $vars['form'] = new mySearchForm();
    
    if ($requestQuery = $this->getService('request')->getParameter('query'))
    {
      $vars['form']->setDefault('query', $requestQuery);
    }
    
    return $vars;
  }

  public function isCachable()
  {
    return parent::isCachable() && !$this->getService('request')->hasParameter('query');
  }

}