<?php

echo _open('div.dm_box.big.seo_validation');

echo _tag('h1.title', __('Seo improvements'));

echo _open('div.dm_box_inner');

foreach($duplicated as $meta => $values)
{
  echo _tag('h2', ucfirst(__('Duplicated %1%', array('%1%' => dmString::pluralize($meta)))));
  echo _open('div.dm_table_wrap')._open('table.dm_table');
  echo _tag('thead',
    _tag('tr', _tag('th', $meta)._tag('th', __('Pages')))
  );

  toggle_init();
  foreach($values as $value => $pages)
  {
    echo _open('tr'.toggle('.even'));
    echo _tag('td', $value);
    echo _open('td');
    echo _open('ul');
    foreach($pages as $page)
    {
      echo _tag('li',
        _tag('a.s16.s16_next.dm_toggler', $page->get('name')).
        _tag('div.actions.none.dm_toggled', get_partial('pageActions', array('page' => $page, 'meta' => $meta)))
      );
    }
    echo _close('ul');
    echo _close('td');
    echo _close('tr');
  }

  echo _close('table')._close('div');
}

echo _close('div');

echo _close('div');