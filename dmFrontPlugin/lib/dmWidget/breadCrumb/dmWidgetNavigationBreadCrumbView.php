<?php

class dmWidgetNavigationBreadCrumbView extends dmWidgetPluginView
{

	public function getRequiredVars()
	{
    return array('separator', 'includeCurrent');
	}

	public function getViewVars($vars = array())
  {
    $vars = parent::getViewVars($vars);

    $currentPage = dmContext::getInstance()->getPage();

    $treeObject = dmDb::table('DmPage')->getTree();
    $treeObject->setBaseQuery(dmDb::table('DmPage')->createQuery('p')->withI18n());

    $ancestors = $currentPage->getNode()->getAncestors();

    $treeObject->resetBaseQuery();

    $vars['pages'] = $ancestors ? $ancestors : array();

    if ($vars['includeCurrent'])
    {
    	$vars['pages'][] = $currentPage;
    }

    return $vars;
  }

}