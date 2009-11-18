<?php

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

foreach($pager->getResults() as $result)
{
  echo £('li.search_result.ml20.mb5',
  
    £('span.score.mr10', round(100*$result->getScore()).'%').
    
    £link($result->getPage())
    ->text(
      £('strong', $result->getPage()->name).
      ($result->getPage()->description
      ? £('span.ml10', $result->getPage()->description)
      : '')
    )
  );
}

echo £c('ol');