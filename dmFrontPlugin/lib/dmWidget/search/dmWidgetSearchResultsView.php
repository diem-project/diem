<?php

class dmWidgetSearchResultsView extends dmWidgetPluginView
{
  protected
  $isIndexable = false;

  protected function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);
    
    $vars['form'] = new mySearchForm();
    
    $vars['form']->bind(array('query' => $this->getService('request')->getParameter('query')));
    
    $vars['query'] = $vars['form']->getValue('query');
    
    $vars['pager'] = $this->getResultsPager($vars);
    
    return $vars;
  }
  
  protected function getResultsPager(array $vars)
  {
    $results = $this->getService('search_engine')->search($vars['query']);

    if(empty($results))
    {
      return null;
    }

    $pager = new dmSearchPager($results, dmArray::get($vars, 'maxPerPage', 99999));
    $pager->setPage($this->getService('request')->getParameter('page', 1));
    $pager->init();
    
    return $this->getService('front_pager_view')
    ->setPager($pager)
    ->setOption('navigation_top', dmArray::get($vars, 'navTop'))
    ->setOption('navigation_bottom', dmArray::get($vars, 'navBottom'))
    ->setBaseHref($this->getService('request')->getUri());
  }

}