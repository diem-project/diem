<?php

echo £o('div.dm_box.big.search_engine');

echo £('h1.title', __('Internal search engine'));

echo £o('div.dm_box_inner');

echo £('div.search_actions.clearfix',
  £('div.dm_third',
    £('h2.mb10', 'Search in the index').
		$form->open('method=get').
		$form['query']->renderLabel(__('Query')).
		$form['query'].
		sprintf('<input type="submit" name="%s" />', dm::getI18n()->__('Search')).
		$form->close()
  ).
  £('div.dm_third',
    £('h2.mb10', 'Index state').
    get_partial('state', array('index' => $index))
  ).
  £('div.dm_third',
    £('h2.mb10', 'Index maintenance').
    £link('dmSearchEngine/reload')->text(__('Reload index'))
  )
);

if ($query)
{
  echo £('p', 'Search completed in '.$time.'ms');
	include_partial('results', array('pager' => $pager));
}

echo £c('div');

echo £c('div');