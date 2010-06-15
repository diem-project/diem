<?php
/*
 * Variables available :
 * - $query (string)        the searched query
 * - $form  (mySearchForm)  the search form
 * - $pager (dmSearchPager) the search pager
 */

if (!$pager)
{
  echo _tag('h1', __('No results for "%1%"', array('%1%' => escape($query))));
  return;
}

echo _tag('h1', __('Results %1% to %2% of %3%', array(
  '%1%' => $pager->getFirstIndice(),
  '%2%' => $pager->getLastIndice(),
  '%3%' => $pager->getNbResults()
)));

echo $pager->renderNavigationTop();

echo _open('ol.search_results start='.$pager->getFirstIndice());

foreach($pager as $result)
{
  $page = $result->getPage();
  
  echo _tag('li.search_result',
  
    _tag('span.score', ceil(100*$result->getScore()).'%').
    
    _link($page)->text(
      _tag('span.page_name', escape($page->name)).
      dmString::truncate($result->getPageContent(), 200)
    )
  );
}

echo _close('ol');

echo $pager->renderNavigationBottom();