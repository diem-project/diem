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

    $vars['links'] = $this->getLinks($vars['includeCurrent']);
    
    $vars['nbLinks'] = count($vars['links']);

    return $vars;
  }

  protected function getLinks($includeCurrent = true)
  {
    $pages = $this->getPages($includeCurrent);
    $links = array();
    $helper = $this->getHelper();
    
    foreach($pages as $key => $page)
    {
      $links[$key] = $helper->link($page);
    }

    /*
     * Allow listeners of dm.bread_crumb.filter_links event
     * to filter and modify the links list
     */
    return $this->context->getEventDispatcher()->filter(
      new sfEvent($this, 'dm.bread_crumb.filter_links', array('page' => $this->context->getPage())),
      $links
    )->getReturnValue();
  }
  
  protected function getPages($includeCurrent = true)
  {
    $treeObject = dmDb::table('DmPage')->getTree();
    
    $baseQuery = dmDb::table('DmPage')
                 ->createQuery('p')
                 ->withI18n();
    
    if (!isset($this->compiledVars['includeInactivePages']) || !$this->compiledVars['includeInactivePages'])
    {
      $baseQuery->where('pTranslation.is_active = ?', true);
    }
    
    $treeObject->setBaseQuery($baseQuery);

    if(!$currentPage = $this->context->getPage())
    {
      throw new dmException('No current page');
    }

    $ancestors = $currentPage->getNode()->getAncestors();
    
    $ancestors = $ancestors ? $ancestors : array();

    $treeObject->resetBaseQuery();

    if ($includeCurrent)
    {
      $ancestors[] = $currentPage;
    }

    $pages = array();
    foreach($ancestors as $page)
    {
      $pages[$page->get('module').'/'.$page->get('action').'/'.$page->getRecordId()] = $page;
    }

    /*
     * Allow listeners of dm.bread_crumb.filter_pages event
     * to filter and modify the pages list
     */
    return $this->context->getEventDispatcher()->filter(
      new sfEvent($this, 'dm.bread_crumb.filter_pages', array('page' => $this->context->getPage())),
      $pages
    )->getReturnValue();
  }

  protected function doRender()
  {
    if ($this->isCachable() && $cache = $this->getCache())
    {
      return $cache;
    }
    
    $vars = $this->getViewVars();
    $helper = $this->getHelper();
    
    $html = '<ol>';

    $it = 0;
    foreach($vars['links'] as $link)
    {
      $html .= $helper->tag('li', $link);
    
      if ($vars['separator'] && (++$it < $vars['nbLinks']))
      {
        $html .= $helper->tag('li.bread_crumb_separator', $vars['separator']);
      }
    }
    
    $html .= '</ol>';

    if ($this->isCachable())
    {
      $this->setCache($html);
    }
    
    return $html;
  }

}
