<?php

if (!$pager)
{
	echo £('h2', __('No results'));
	return;
}

//include_partial('pager', array('pager' => $pager));

echo £('h2',
  sprintf('Results %d to %d of %d', $pager->getFirstIndice(), $pager->getLastIndice(), $pager->getNbResults())
);

echo £o("ol.search_results.clearfix start=".$pager->getFirstIndice());

foreach($pager->getResults() as $result)
{
  echo £("li.search_result.ml20.mb5",
		£("span.score.mr10", round(100*$result->getScore())."%").
		£link('app:front/'.$result->getPage()->slug)
		->name(£('strong', $result->getPage()->name).£('span.ml10', $result->getPage()->description))
  );
}

echo £c("ol");

//include_partial('pager', array('pager' => $pager));