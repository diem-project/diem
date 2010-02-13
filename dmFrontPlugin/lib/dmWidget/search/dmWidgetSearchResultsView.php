<?php

class dmWidgetSearchResultsView extends dmWidgetPluginView
{
  protected
  $isIndexable = false;

  protected function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);
    
    $vars['form'] = new mySearchForm;
    
    $vars['form']->bindRequest($this->context->getRequest());
    
    $vars['query'] = $vars['form']->getValue('query');
    
    $vars['pager'] = $this->getResultsPager($vars);
    
    return $vars;
  }
  
  protected function getResultsPager(array $vars)
  {
    $this->index = $this->context->get('search_engine');
    
    if(count($results = $this->index->search($vars['query'])))
    {
      $pager = new dmSearchPager($results, dmArray::get($vars, 'maxPerPage', 99999));
      $pager->setPage($this->context->getRequest()->getParameter('page', 1));
      $pager->init();
    }
    else
    {
      $pager = null;
    }
    
    return $pager;
  }

}