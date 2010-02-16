<?php

echo _open('div.dm_page_manager.ui-tabs.ui-widget.ui-widget-content.ui-corner-all.mt10');

include_partial('dmPage/tabs');

echo _open('table#dm_page_meta_table', array('json' => array(
  'translation_url' => _link('dmPage/tableTranslation')->getHref()
)));

echo _open('thead')._open('tr');

foreach($fields as $field)
{
  echo _tag('th', __($field));
}

echo _close('thead')._close('tr');

echo _open('tbody');

foreach($pages as $page)
{
  echo '<tr>';
  foreach($fields as $field)
  {
    echo sprintf('<td rel="%s">%s</td>', $field, $page[$field]);
  }
  echo '</tr>';
}

echo _close('tbody');

echo _close('table');

echo _close('div');