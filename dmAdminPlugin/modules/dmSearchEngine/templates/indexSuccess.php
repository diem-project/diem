<?php

echo £o('div.dm_box.big.search_engine');

echo £('h1.title', __('Internal search engine'));

echo £o('div.dm_box_inner');

echo £('div.search_actions.clearfix',
  £('div.dm_third',
    £('h2.mb10', __('Search')).
    $form->open('method=get').
    $form['query']->renderLabel(__('Query')).
    $form['query'].
    sprintf('<input type="submit" name="%s" />', __('Search')).
    $form->close()
  ).
  £('div.dm_third',
    £('h2.mb10', __('Index state')).
    get_partial('state', array('engine' => $engine))
  ).
  £('div.dm_third',
    £('h2.mb10', __('Index maintenance')).
    £link('dmSearchEngine/reload')->text(__('Reload index'))
  )
);

if ($query)
{
  echo £('p', __('Search completed in %1% ms', array('%1%' => $time)));
  include_partial('results', array('pager' => $pager));
}

echo £c('div');

echo £c('div');