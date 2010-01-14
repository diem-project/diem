<?php

class dmWidgetNavigationBreadCrumbView extends dmWidgetPluginView
{
  protected
  $isIndexable = false;

  public function getRequiredVars()
  {
    return array('separator', 'includeCurrent');
  }

  protected function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);

    $vars['pages'] = $this->getPages($vars['includeCurrent']);
    
    $vars['nbPages'] = count($vars['pages']);

    return $vars;
  }
  
  protected function getPages($includeCurrent = true)
  {
    $treeObject = dmDb::table('DmPage')->getTree();
    $treeObject->setBaseQuery(dmDb::table('DmPage')->createQuery('p')->withI18n());

    $ancestors = $this->context->getPage()->getNode()->getAncestors();

    $ancestors = $ancestors ? $ancestors : array();

    $treeObject->resetBaseQuery();
    
    if ($includeCurrent)
    {
      $ancestors[] = $this->context->getPage();
    }

    $pages = array();
    foreach($ancestors as $page)
    {
      $pages[$page->get('module').'.'.$page->get('action')] = $page;
    }
    
    /*
     * Allow listeners of dm.bread_crumb.filter_pages event
     * to filter and modify the pages list
     */
    return $this->context->getEventDispatcher()->filter(new sfEvent($this, 'dm.bread_crumb.filter_pages'), $pages)->getReturnValue();
  }

  protected function doRender()
  {
    if ($this->isCachable() && $cache = $this->getCache())
    {
      return $cache;
    }
    
    $vars = $this->getViewVars();
    
    $html = '<ol>';

    $it = 0;
    foreach($vars['pages'] as $page)
    {
      $html .= $this->context->getHelper()->£('li', $this->context->getHelper()->£link($page)->render());
    
      if ($vars['separator'] && (++$it < $vars['nbPages']))
      {
        $html .= '<li class="bread_crumb_separator">'.$vars['separator'].'</li>';
      }
    }
    
    $html .= '</ol>';
    
    return $html;
  }

}