<?php

echo £o('div.dm_box.big.seo_validation');

echo £('h1.title', __('Seo improvments'));

echo £o('div.dm_box_inner');

foreach($duplicated as $meta => $values)
{
	echo £('h2', ucfirst(__('Duplicated %1%', array('%1%' => dmString::pluralize($meta)))));
	echo £o('div.dm_table_wrap').£o('table.dm_table');
	echo £('thead',
	  £('tr', £('th', $meta).£('th', __('Pages')))
	);

	toggle_init();
	foreach($values as $value => $pages)
	{
		echo £o('tr'.toggle('.even'));
		echo £('td', $value);
		echo £o('td');
		echo £o('ul');
		foreach($pages as $page)
		{
			echo £('li',
			  £('a.s16.s16_next.dm_toggler', $page->get('name')).
        £('div.actions.none.dm_toggled', get_partial('pageActions', array('page' => $page, 'meta' => $meta)))
			);
		}
		echo £c('ul');
		echo £c('td');
    echo £c('tr');
	}

  echo £c('table').£c('div');
}

echo £c('div');

echo £c('div');