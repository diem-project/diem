<?php
/*
 * Variables available :
 * - $query (string)        the searched query
 * - $form  (mySearchForm)  the search form
 * - $pager (dmSearchPager) the search pager
 */

if (!$pager)
{
  echo £('h1', __('No results for "%1%"', array('%1%' => $query)));
  return;
}

echo £('h1', __('Results %1% to %2% of %3%', array(
  '%1%' => $pager->getFirstIndice(),
  '%2%' => $pager->getLastIndice(),
  '%3%' => $pager->getNbResults()
)));

echo £o('ol.search_results.clearfix start='.$pager->getFirstIndice());

foreach($pager as $result)
{
  $page = $result->getPage();
  
  echo £('li.search_result.ml20.mb5',
  
    £('span.score.mr10', ceil(100*$result->getScore()).'%').
    
    £link($page)->text(
      £('strong', $page->name).
      ($page->description ? £('span.ml10', $page->description) : '')
    )
  );
}

echo £c('ol');