<?php

if (!$pager)
{
  echo _tag('h2', __('No results'));
  return;
}

//include_partial('pager', array('pager' => $pager));

echo _tag('h2',
  __('%1% - %2% of %3%', array('%1%' => $pager->getFirstIndice(), '%2%' => $pager->getLastIndice(), '%3%' => $pager->getNbResults()))
);

echo _open("ol.search_results.clearfix start=".$pager->getFirstIndice());

foreach($pager->getResults() as $result)
{
  echo _tag("li.search_result.ml20.mb5",
    _tag("span.score.mr10", round(100*$result->getScore())."%").
    _link('app:front/'.$result->getPage()->slug)
    ->text(_tag('strong', $result->getPage()->name)._tag('span.ml10', $result->getPage()->description))
  );
}

echo _close("ol");

//include_partial('pager', array('pager' => $pager));
