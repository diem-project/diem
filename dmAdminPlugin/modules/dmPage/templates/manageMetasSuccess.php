<?php

echo _open('div.dm_page_manager.ui-tabs.ui-widget.ui-widget-content.ui-corner-all.mt10');

include_partial('dmPage/tabs');

echo
$form->open('.dm_meta_fields.ui-corner-all').
$form['fields']->field().
$form->submit(__('Select columns')).
$form->renderHiddenFields().
$form->close();

echo _open('table#dm_page_meta_table', array('json' => array(
  'translation_url' => _link('dmPage/tableTranslation')->getHref(),
  'edition_url' => _link('dmPage/editField')->getHref(),
  'toggle_url' => _link('dmPage/toggleBoolean')->getHref()
)));

echo _open('thead')._open('tr');

foreach($fields as $field)
{
  echo _tag('th', $pageMetaView->renderField($field));
}

echo _close('thead')._close('tr');

echo _open('tbody');

foreach($pages as $page)
{
  $pageMetaView->setPage($page);
  
  echo _open('tr#'.$page['id']);
  foreach($fields as $field)
  {
    echo $pageMetaView->renderMeta($field);
  }
  echo '</tr>';
}

echo _close('tbody');

echo _close('table');

echo _close('div');