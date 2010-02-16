<?php

echo _open('div.dm_page_manager.ui-tabs.ui-widget.ui-widget-content.ui-corner-all.mt10');

include_partial('dmPage/tabs', array('current' => 'tree'));

echo _tag('div#dm_full_page_tree.clearfix', $tree->render());

echo _close('div');