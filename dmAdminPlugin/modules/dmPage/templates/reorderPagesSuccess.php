<?php

echo _open('div.dm_page_manager.ui-tabs.ui-widget.ui-widget-content.ui-corner-all.mt10');

include_partial('dmPage/tabs');

echo once_per_session(
  _tag('p.help_box', _tag('span.s16.s16_help.block', __('Drag pages around to move and sort them.')))
);

echo _tag('div#dm_full_page_tree.clearfix.dm', array('json' => array(
  'move_url' => _link('dmPage/move')->getHref()
)), $tree->render());

echo _close('div');