<?php

class dmWidgetAdvancedSearchResultsView extends dmWidgetPluginView
{
  protected
  $isIndexable = false;

  public function getRequiredVars()
  {
    return array('maxPerPage');
  }

  public function getViewVars(array $vars = array())
  {
    $vars = parent::getViewVars($vars);
    
    $form = new mySearchForm;
    
    $form->bindRequest($this->context->getRequest());
    
    $vars['query'] = $form->getValue('query');
    
    $vars['pager'] = $this->getResultsPager($vars);
    
    return $vars;
  }
  
  protected function getResultsPager(array $vars)
  {
    $this->index = $this->context->get('search_engine');
    
    if(count($results = $this->index->search($vars['query'])))
    {
      $pager = new dmSearchPager($results, $vars['maxPerPage']);
      $pager->setPage($this->context->getParameter('page', 1));
      $pager->init();
    }
    else
    {
      $pager = null;
    }
    
    return $pager;
  }

}