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

    $currentPage = $this->context->getPage();

    $treeObject = dmDb::table('DmPage')->getTree();
    $treeObject->setBaseQuery(dmDb::table('DmPage')->createQuery('p')->withI18n());

    $ancestors = $currentPage->getNode()->getAncestors();

    $treeObject->resetBaseQuery();

    $vars['pages'] = $ancestors ? $ancestors : array();

    if ($vars['includeCurrent'])
    {
      $vars['pages'][] = $currentPage;
    }
    
    $vars['nbPages'] = count($vars['pages']);

    return $vars;
  }

  protected function doRender()
  {
    if ($this->isCachable() && $cache = $this->getCache())
    {
      return $cache;
    }
    
    $vars = $this->getViewVars();
    
    $html = '<ol>';

    foreach($vars['pages'] as $position => $page)
    {
      $html .= dmHelper::£('li', dmFrontLinkTag::build($page)->render());
    
      if ($vars['separator'] && ($position < ($vars['nbPages']-1)))
      {
        $html .= dmHelper::£('li', $vars['separator']);
      }
    }
    
    $html .= '</ol>';
    
    return $html;
  }

}